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
use Illuminate\Support\Facades\DB;
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
        return view('admin.zoom.create', [
            'meeting' => null,
            'subjects' => $this->pickerSubjects(),
            'connected' => $this->zoom->isConfigured(),
            'fake' => $this->zoom->isFake(),
        ]);
    }

    /** A meeting that was made directly in Zoom: tell the LMS which modules it covers. */
    public function linkForm()
    {
        return view('admin.zoom.create', [
            'meeting' => null,
            'linking' => true,
            'subjects' => $this->pickerSubjects(),
            'connected' => $this->zoom->isConfigured(),
            'fake' => $this->zoom->isFake(),
        ]);
    }

    public function link(Request $request)
    {
        // Only the link or ID and the modules are needed: the LMS reads title, date, time and length from Zoom.
        // The four details can be typed as a fallback (all four or none).
        $data = $this->validated($request, [
            'zoom_ref' => 'required|string|max:500',
            'topic' => 'nullable|string|max:200|required_with:date,time,duration',
            'date' => 'nullable|date|required_with:topic,time,duration',
            'time' => 'nullable|date_format:H:i|required_with:topic,date,duration',
            'duration' => 'nullable|integer|min:10|max:480|required_with:topic,date,time',
        ]);
        $id = ZoomService::meetingIdFrom($data['zoom_ref']);

        if (! $id) {
            return back()->withInput()->withErrors(['zoom_ref' => 'That does not look like a Zoom meeting link or ID. Paste the link from the invitation, or the 9 to 11 digit meeting ID.']);
        }

        if ($existing = ZoomMeeting::where('zoom_meeting_id', $id)->first()) {
            return back()->withInput()->withErrors(['zoom_ref' => 'This meeting is already in the LMS ("'.$existing->topic.'"). Open it from the Zoom Classes list.']);
        }

        if (filled($data['topic'] ?? null)) {
            // typed by hand
            $topic = $data['topic'];
            $start = $this->startFrom($data);
            $duration = (int) $data['duration'];
        } else {
            try {
                $details = $this->zoom->meetingDetails($id);
            } catch (RuntimeException $e) {
                return back()->withInput()->with('show_manual', true)->with('error',
                    'The LMS could not read this meeting from Zoom ('.$e->getMessage().'). You can type the title, date, time and length below instead.');
            }

            if (! $details) {
                return back()->withInput()->with('show_manual', true)->withErrors(['zoom_ref' =>
                    'Zoom has no meeting with this ID in the campus account, or it has no fixed start time. Check the ID, or type the title, date, time and length below.']);
            }

            [$topic, $start, $duration] = [$details['topic'], $details['start'], $details['duration']];
        }

        $past = $start->copy()->addMinutes($duration)->isPast();
        $isLink = str_contains($data['zoom_ref'], '/');

        $meeting = DB::transaction(function () use ($data, $id, $topic, $start, $duration, $past, $isLink) {
            $meeting = ZoomMeeting::create([
                'zoom_meeting_id' => $id,
                'topic' => $topic,
                'start_time' => $start,
                'duration_minutes' => $duration,
                'join_url' => $isLink ? trim($data['zoom_ref']) : 'https://zoom.us/j/'.$id,
                'passcode' => null,
                'linked' => true,   // made in Zoom: the LMS never edits or cancels it there
                // a class that is already over goes straight to "fetching attendance"
                'status' => $past ? 'ended' : 'scheduled',
                'ended_at' => $past ? $start->copy()->addMinutes($duration) : null,
                'created_by' => auth('admin')->id(),
            ]);
            $meeting->subjects()->sync(array_unique($data['subject_ids']));

            return $meeting;
        });

        if (! $past) {
            return redirect()->route('admin.zoom.show', $meeting->id)
                ->with('success', 'Meeting linked. When it ends, Zoom tells the LMS and the attendance is saved on its own.');
        }

        $result = $this->attendance->run($meeting->fresh());

        return redirect()->route('admin.zoom.show', $meeting->id)->with(
            $result === 'saved' ? 'success' : 'error',
            match ($result) {
                'saved' => 'Meeting linked and its attendance was fetched from Zoom and saved.',
                'waiting' => 'Meeting linked, but Zoom does not have a participant list for it yet. The LMS will keep trying every few minutes. If the meeting ID was wrong, delete it and link it again.',
                default => 'Meeting linked, but Zoom has no participant list for it: '.($meeting->fresh()->last_error ?? 'unknown reason'),
            }
        );
    }

    /** The same form, filled in. Only until the class starts. */
    public function edit($id)
    {
        $meeting = ZoomMeeting::with('subjects:id')->findOrFail($id);

        if (! $meeting->canBeEdited()) {
            return redirect()->route('admin.zoom.show', $meeting->id)
                ->with('error', 'This class can only be edited before it starts.');
        }

        return view('admin.zoom.create', [
            'meeting' => $meeting,
            'subjects' => $this->pickerSubjects(),
            'connected' => $this->zoom->isConfigured(),
            'fake' => $this->zoom->isFake(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $meeting = ZoomMeeting::findOrFail($id);

        if (! $meeting->canBeEdited()) {
            return redirect()->route('admin.zoom.show', $meeting->id)
                ->with('error', 'This class can only be edited before it starts.');
        }

        $data = $this->validated($request);
        $start = $this->startFrom($data);

        if ($start->lt(now()->subMinutes(15))) {
            return back()->withInput()->withErrors(['date' => 'The start time is in the past. Pick a time from now onwards.']);
        }

        // Zoom first: if Zoom refuses, nothing changes in the LMS and the two never disagree.
        // A meeting that was made in Zoom is only linked here, so the LMS never changes it in Zoom.
        if (! $meeting->linked) {
            try {
                $this->zoom->updateMeeting($meeting->zoom_meeting_id, $data['topic'], $start, (int) $data['duration']);
            } catch (RuntimeException $e) {
                return back()->withInput()->with('error', $e->getMessage());
            }
        }

        DB::transaction(function () use ($meeting, $data, $start) {
            $meeting->update([
                'topic' => $data['topic'],
                'start_time' => $start,
                'duration_minutes' => (int) $data['duration'],
            ]);
            $meeting->subjects()->sync(array_unique($data['subject_ids']));
        });

        return redirect()->route('admin.zoom.show', $meeting->id)
            ->with('success', 'Zoom class updated. The join link stays the same.');
    }

    private function pickerSubjects()
    {
        return Subject::with(['course.faculty', 'level', 'semester'])
            ->orderBy('code')
            ->get()
            ->sortBy(fn ($s) => sprintf('%s|%s|%s|%s|%s',
                $s->course?->faculty?->name, $s->course?->name, $s->level?->name, $s->semester?->name, $s->code))
            ->values();
    }

    private function validated(Request $request, array $override = []): array
    {
        return $request->validate(array_merge([
            'topic' => 'required|string|max:200',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'duration' => 'required|integer|min:10|max:480',
            'subject_ids' => 'required|array|min:1',
            'subject_ids.*' => 'integer|exists:subjects,id',
        ], $override), [
            'subject_ids.required' => 'Add at least one module for this class.',
            'subject_ids.min' => 'Add at least one module for this class.',
        ]);
    }

    private function startFrom(array $data): Carbon
    {
        return Carbon::parse($data['date'].' '.$data['time'], config('app.timezone'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $start = $this->startFrom($data);

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

        // Cancel it in Zoom too, but only while the class has not been held, and never a meeting that was
        // made in the Zoom app (it is only linked here).
        if ($meeting->status === 'scheduled' && ! $meeting->linked) {
            try {
                $this->zoom->deleteMeeting($meeting->zoom_meeting_id);
            } catch (Throwable $e) {
                $note = ' It could not be cancelled in Zoom ('.$e->getMessage().'), so delete it there too.';
            }
        }

        $meeting->delete();   // attendance already saved stays in the Attendance pages

        return redirect()->route('admin.zoom.index')->with('success', 'Zoom class deleted.'.($meeting->linked ? ' The meeting itself was not touched in Zoom.' : '').$note);
    }
}
