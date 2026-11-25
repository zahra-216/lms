<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\ZoomMeeting;
use App\Models\ZoomParticipant;
use App\Support\RegistrationMatcher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Throwable;

/**
 * Turns "who joined the Zoom class" into present / absent marks in the normal attendance table.
 *
 * Rules (agreed with the campus):
 *  - A name counts as a student when it contains a registration number that exists in the LMS,
 *    in any spelling of capitals, spaces, dashes or slashes ("ttmc ml dip hrm-o 24 02 Kamal" works).
 *    A last number written without its leading zeros ("26/ 13" for "26/013") or with the letter O for a zero also works.
 *  - Each ticked module marks the students of that module's course, level and CURRENT semester
 *    (the same group the Attendance page shows). Joined = present, everyone else = absent.
 *  - A mark made by hand is never overwritten. Only marks that came from Zoom are updated.
 *  - The date is the day the class started.
 */
class ZoomAttendance
{
    public function __construct(private ZoomService $zoom)
    {
    }

    /** Capitals and digits only, so spaces, dashes, slashes and dots do not matter. */
    public static function normalize(string $text): string
    {
        return RegistrationMatcher::normalize($text);
    }

    /**
     * Ask Zoom for the list and save attendance. Safe to run again.
     *
     * @return string saved | waiting | failed
     */
    public function run(ZoomMeeting $meeting): string
    {
        $meeting->increment('attempts');

        try {
            $rows = $this->zoom->participants($meeting->zoom_meeting_id, $meeting->zoom_uuid);
            $error = $rows ? null : 'Zoom has not given a participant list for this class yet.';
        } catch (Throwable $e) {
            $rows = [];
            $error = $e->getMessage();
            Log::warning("Zoom attendance for meeting {$meeting->zoom_meeting_id}: {$error}");
        }

        if (! $rows) {
            return $this->retryOrFail($meeting, $error);
        }

        DB::transaction(function () use ($meeting, $rows) {
            // The day Zoom says the class really started. If Zoom did not tell us, the first person to join
            // marks the start. Kept once set, so fetching again never moves the attendance to another day.
            if (! $meeting->held_at) {
                $first = collect($rows)->pluck('join_time')->filter()->min();
                if ($first) {
                    $meeting->update(['held_at' => $first]);
                }
            }

            $this->storeParticipants($meeting, $rows);
            $this->applyAttendance($meeting);

            $meeting->update([
                'status' => 'processed',
                'processed_at' => now(),
                'next_try_at' => null,
                'last_error' => null,
            ]);
        });

        return 'saved';
    }

    private function retryOrFail(ZoomMeeting $meeting, ?string $error): string
    {
        if ($meeting->attempts >= config('services.zoom.max_attempts')) {
            $meeting->update(['status' => 'failed', 'next_try_at' => null, 'last_error' => $error]);

            return 'failed';
        }

        $meeting->update([
            'status' => 'ended',
            'next_try_at' => now()->addMinutes(config('services.zoom.retry_minutes')),
            'last_error' => $error,
        ]);

        return 'waiting';
    }

    /** Replace the saved list with Zoom's, keeping names the admin matched by hand. */
    private function storeParticipants(ZoomMeeting $meeting, array $rows): void
    {
        $byHand = $meeting->participants()->where('matched_by', 'manual')->get()
            ->mapWithKeys(fn ($p) => [self::normalize($p->name) => $p->student_id]);

        $meeting->participants()->delete();

        $matcher = $this->matcher();

        foreach ($rows as $row) {
            $key = self::normalize($row['name']);
            $studentId = $byHand[$key] ?? null;
            $how = $studentId ? 'manual' : null;

            if (! $studentId) {
                $studentId = $matcher->match($row['name']);
                $how = $studentId ? 'auto' : null;
            }

            $meeting->participants()->create([
                'name' => $row['name'] !== '' ? $row['name'] : '(no name)',
                'email' => $row['email'],
                'join_time' => $row['join_time'],
                'leave_time' => $row['leave_time'],
                'duration_seconds' => $row['duration'],
                'student_id' => $studentId,
                'matched_by' => $how,
            ]);
        }
    }

    /** Finds a registration number inside the name someone joined with (see RegistrationMatcher). */
    public function matcher(): RegistrationMatcher
    {
        return new RegistrationMatcher(Student::select('id', 'registration_no')->get());
    }

    /**
     * Save present / absent for every module of the class. Run again after a manual match.
     */
    public function applyAttendance(ZoomMeeting $meeting): void
    {
        $date = $meeting->attendanceDate();
        $joined = $meeting->participants()->whereNotNull('student_id')->pluck('student_id')->unique()->flip();

        foreach ($meeting->subjects as $subject) {
            $students = $this->subjectStudents($subject)->pluck('id');
            $existing = Attendance::where('subject_id', $subject->id)->where('date', $date)
                ->whereIn('student_id', $students)->get()->keyBy('student_id');

            foreach ($students as $studentId) {
                $status = $joined->has($studentId) ? 'present' : 'absent';
                $row = $existing->get($studentId);

                if (! $row) {
                    Attendance::create([
                        'student_id' => $studentId, 'subject_id' => $subject->id, 'date' => $date,
                        'status' => $status, 'source' => 'zoom', 'marked_by' => null,
                    ]);
                } elseif ($row->source === 'zoom' && $row->status !== $status) {
                    $row->update(['status' => $status]);
                }
                // anything else was marked by hand: left alone
            }
        }
    }

    /** The students the Attendance page lists for a module: its course, level and current semester. */
    public function subjectStudents($subject)
    {
        return Student::where('course_id', $subject->course_id)
            ->where('level_id', $subject->level_id)
            ->where('semester_id', $subject->semester_id)
            ->orderBy('registration_no')
            ->get(['id', 'registration_no', 'name', 'course_id', 'level_id', 'semester_id']);
    }

    /**
     * The admin typed a registration number for a name Zoom showed.
     *
     * @throws InvalidArgumentException with a message the admin can read
     */
    public function assign(ZoomMeeting $meeting, ZoomParticipant $participant, string $registrationNo): void
    {
        $id = $this->matcher()->match($registrationNo);

        $student = $id ? Student::select('id', 'registration_no', 'course_id', 'level_id', 'semester_id')->find($id) : null;

        if (! $student) {
            throw new InvalidArgumentException('No student has that registration number.');
        }

        $inClass = $meeting->subjects->contains(fn ($subject) => $subject->course_id === $student->course_id
            && $subject->level_id === $student->level_id
            && $subject->semester_id === $student->semester_id);

        if (! $inClass) {
            throw new InvalidArgumentException("{$student->registration_no} is not in any of this class's modules (course, level and semester), so there is nothing to mark.");
        }

        DB::transaction(function () use ($meeting, $participant, $student) {
            // the same person may have joined more than once under the same name
            $meeting->participants()
                ->get()
                ->filter(fn ($p) => self::normalize($p->name) === self::normalize($participant->name))
                ->each->update(['student_id' => $student->id, 'matched_by' => 'manual']);

            $this->applyAttendance($meeting);
        });
    }

    /**
     * Numbers for the class page, per module.
     *
     * @return array<int, array{subject: mixed, total: int, present: int, absent: int, by_hand: int, students: \Illuminate\Support\Collection}>
     */
    public function summary(ZoomMeeting $meeting): array
    {
        $date = $meeting->attendanceDate();
        $out = [];

        foreach ($meeting->subjects as $subject) {
            $students = $this->subjectStudents($subject);
            $rows = Attendance::where('subject_id', $subject->id)->where('date', $date)
                ->whereIn('student_id', $students->pluck('id'))->get()->keyBy('student_id');

            $list = $students->map(function ($s) use ($rows) {
                $r = $rows->get($s->id);

                return (object) [
                    'id' => $s->id,
                    'registration_no' => $s->registration_no,
                    'name' => $s->name,
                    'status' => $r?->status,
                    'source' => $r?->source,
                ];
            });

            $out[] = [
                'subject' => $subject,
                'total' => $students->count(),
                'present' => $list->where('status', 'present')->count(),
                'absent' => $list->where('status', 'absent')->count(),
                'by_hand' => $list->where('source', 'manual')->count(),
                'students' => $list,
            ];
        }

        return $out;
    }
}
