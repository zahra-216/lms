<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\ZoomMeeting;
use App\Models\ZoomParticipant;
use App\Services\ZoomAttendance;
use App\Services\ZoomService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

/**
 * Admin: make Zoom classes from the LMS and see the attendance Zoom gave back.
 */
class ZoomClassController extends Controller
{
    public function __construct(private ZoomService $zoom, private ZoomAttendance $attendance)
    {
    }

    public function index()
    {
        $meetings = ZoomMeeting::withCount(['subjects', 'participants'])
            ->with('subjects:id,code,name')
            ->orderByDesc('start_time')
            ->paginate(15);

        return view('admin.zoom.index', [
            'meetings' => $meetings,
            'connected' => $this->zoom->isConfigured(),
            'fake' => $this->zoom->isFake(),
        ]);
    }

    public function create()
    {
        $subjects = Subject::with(['course.faculty', 'level', 'semester'])
            ->orderBy('code')
            ->get()
            ->sortBy(fn ($s) => sprintf('%s|%s|%s|%s|%s',
                $s->course?->faculty?->name, $s->course?->name, $s->level?->name, $s->semester?->name, $s->code))
            ->values();

        return view('admin.zoom.create', [
            'subjects' => $subjects,
            'connected' => $this->zoom->isConfigured(),
            'fake' => $this->zoom->isFake(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'topic' => 'required|string|max:200',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'duration' => 'required|integer|min:10|max:480',
            'subject_ids' => 'required|array|min:1',
            'subject_ids.*' => 'integer|exists:subjects,id',
        ], [
            'subject_ids.required' => 'Add at least one module for this class.',
            'subject_ids.min' => 'Add at least one module for this class.',
        ]);

        $start = Carbon::parse($data['date'].' '.$data['time'], config('app.timezone'));

        // a few minutes in the past is fine (starting a class right now)
        if ($start->lt(now()->subMinutes(15))) {
            return back()->withInput()->withErrors(['date' => 'The start time is in the past. Pick a time from now onwards.']);
        }

        try {
            $zoom = $this->zoom->createMeeting($data['topic'], $start, (int) $data['duration']);
        } catch (RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        try {
            $meeting = ZoomMeeting::create([
                'zoom_meeting_id' => $zoom['id'],
                'topic' => $data['topic'],
                'start_time' => $start,
                'duration_minutes' => (int) $data['duration'],
                'join_url' => $zoom['join_url'],
                'passcode' => $zoom['passcode'],
                'status' => 'scheduled',
                'created_by' => auth('admin')->id(),
            ]);
            $meeting->subjects()->sync(array_unique($data['subject_ids']));
        } catch (Throwable $e) {
            // never leave a meeting in Zoom that the LMS does not know about
            try {
                $this->zoom->deleteMeeting($zoom['id']);
            } catch (Throwable) {
            }

            throw $e;
        }

        return redirect()->route('admin.zoom.show', $meeting->id)
            ->with('success', 'Zoom class created. Share the join link below with the students and lecturers.');
    }

    public function show($id)
    {
        $meeting = ZoomMeeting::with('subjects.course', 'subjects.level', 'subjects.semester')->findOrFail($id);
        $participants = $meeting->participants()->with('student:id,registration_no,name')->orderBy('name')->get();
        $summary = $this->attendance->summary($meeting);

        // students Zoom matched who belong to none of the ticked modules (wrong class, or another semester)
        $classIds = collect($summary)->flatMap(fn ($m) => $m['students']->pluck('id'))->flip();
        $matched = $participants->whereNotNull('student_id')->groupBy('student_id')->map->first()->values();

        return view('admin.zoom.show', [
            'meeting' => $meeting,
            'summary' => $summary,
            'unmatched' => $participants->whereNull('student_id')
                ->groupBy(fn ($p) => ZoomAttendance::normalize($p->name))->map->first()->values(),
            'matched' => $matched,
            'outside' => $matched->filter(fn ($p) => ! $classIds->has($p->student_id))->values(),
            'fake' => $this->zoom->isFake(),
        ]);
    }

    /** "Fetch attendance now" - the admin does not have to wait for the automatic try. */
    public function fetch($id)
    {
        $meeting = ZoomMeeting::findOrFail($id);

        if ($meeting->start_time->isFuture()) {
            return back()->with('error', 'This class has not started yet.');
        }

        $meeting->update(['attempts' => 0]);   // a manual fetch gets a fresh set of tries
        $result = $this->attendance->run($meeting->fresh());

        return back()->with(
            $result === 'saved' ? 'success' : 'error',
            match ($result) {
                'saved' => 'Attendance fetched from Zoom and saved.',
                'waiting' => 'Zoom does not have the participant list yet. The LMS will keep trying every few minutes, or try again here shortly.',
                default => 'Zoom did not give a participant list: '.($meeting->fresh()->last_error ?? 'unknown reason'),
            }
        );
    }

    public function assign(Request $request, $id, $participantId)
    {
        $data = $request->validate(['registration_no' => 'required|string|max:100']);

        $meeting = ZoomMeeting::with('subjects')->findOrFail($id);
        $participant = ZoomParticipant::where('zoom_meeting_id', $meeting->id)->findOrFail($participantId);

        try {
            $this->attendance->assign($meeting, $participant, $data['registration_no']);
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', '"'.$participant->name.'" is now matched and marked present.');
    }

    public function destroy($id)
    {
        $meeting = ZoomMeeting::findOrFail($id);
        $note = '';

        // Cancel it in Zoom too, but only while the class has not been held.
        if ($meeting->status === 'scheduled') {
            try {
                $this->zoom->deleteMeeting($meeting->zoom_meeting_id);
            } catch (Throwable $e) {
                $note = ' It could not be cancelled in Zoom ('.$e->getMessage().'), so delete it there too.';
            }
        }

        $meeting->delete();   // attendance already saved stays in the Attendance pages

        return redirect()->route('admin.zoom.index')->with('success', 'Zoom class deleted.'.$note);
    }
}
