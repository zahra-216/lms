<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lecturer;
use App\Models\Level;
use App\Models\Semester;
use App\Models\Subject;
use App\Models\Timetable;
use App\Notifications\TimetableAssigned;
use App\Services\TimetableReader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TimetableController extends Controller
{
    /** The form, plus the list of saved timetables. */
    public function index(Request $request)
    {
        $courses = Course::active()->with('faculty')->orderBy('name')->get();
        $courseIds = $courses->pluck('id');

        $levels = Level::active()->whereIn('course_id', $courseIds)->orderBy('id')->get()->groupBy('course_id');
        $semesters = Semester::whereIn('course_id', $courseIds)->get()
            ->sortBy(fn ($s) => (int) filter_var($s->name, FILTER_SANITIZE_NUMBER_INT))
            ->groupBy(fn ($s) => $s->course_id.'-'.$s->level_id);
        $subjects = Subject::whereIn('course_id', $courseIds)->orderBy('name')->get(['id', 'code', 'name', 'course_id', 'level_id', 'semester_id'])
            ->groupBy('semester_id');

        $tree = $courses->map(fn ($c) => [
            'id' => $c->id,
            'name' => $c->name,
            'faculty' => $c->faculty->name ?? '',
            'levels' => ($levels[$c->id] ?? collect())->map(fn ($l) => [
                'id' => $l->id,
                'name' => $l->name,
                'semesters' => ($semesters[$c->id.'-'.$l->id] ?? collect())->values()->map(fn ($s) => [
                    'id' => $s->id,
                    'name' => $s->name,
                    'modules' => ($subjects[$s->id] ?? collect())->map(fn ($m) => ['id' => $m->id, 'code' => $m->code, 'name' => $m->name])->values(),
                ])->values(),
            ])->values(),
        ])->values();

        $lecturers = Lecturer::orderBy('name')->get(['id', 'name'])->map(fn ($l) => ['id' => $l->id, 'name' => $l->name])->values();

        $saved = Timetable::with(['course', 'level', 'semester', 'rows'])->latest('updated_at')->get();

        // what the form already holds: a timetable already saved for each course + level + semester
        $existing = $saved->mapWithKeys(fn ($t) => [$t->course_id.'-'.$t->level_id.'-'.$t->semester_id => [
            'id' => $t->id,
            'batch' => $t->batch,
            'mode_note' => $t->mode_note,
            'rows' => $t->sortedRows()->map(fn ($r) => [
                'day' => $r->day,
                'start_time' => substr($r->start_time, 0, 5),
                'end_time' => substr($r->end_time, 0, 5),
                'subject_id' => $r->subject_id,
                'lecturer_id' => $r->lecturer_id,
                'note' => $r->note,
            ])->values(),
        ]]);

        // "Edit" in the list opens that timetable in the form; after a failed save the form keeps what was typed
        $preset = null;
        if (old('course_id')) {
            $preset = ['course_id' => (int) old('course_id'), 'level_id' => (int) old('level_id'), 'semester_id' => (int) old('semester_id')];
        } elseif ($request->filled('edit') && ($t = $saved->firstWhere('id', (int) $request->query('edit')))) {
            $preset = ['course_id' => $t->course_id, 'level_id' => $t->level_id, 'semester_id' => $t->semester_id];
        }

        return view('admin.timetables.index', compact('tree', 'lecturers', 'saved', 'existing', 'preset'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'course_id' => ['required', Rule::exists('courses', 'id')->where('status', 'active')],
            'level_id' => ['required', Rule::exists('levels', 'id')->where('course_id', $request->input('course_id'))],
            'semester_id' => ['required', Rule::exists('semesters', 'id')
                ->where('course_id', $request->input('course_id'))->where('level_id', $request->input('level_id'))],
            'batch' => ['nullable', 'string', 'max:60'],
            'mode_note' => ['nullable', 'string', 'max:150'],
            'rows' => ['required', 'array', 'min:1', 'max:40'],
            'rows.*.day' => ['required', Rule::in(Timetable::DAYS)],
            'rows.*.start_time' => ['required', 'date_format:H:i'],
            'rows.*.end_time' => ['required', 'date_format:H:i'],
            'rows.*.subject_id' => ['required', Rule::exists('subjects', 'id')
                ->where('course_id', $request->input('course_id'))
                ->where('level_id', $request->input('level_id'))
                ->where('semester_id', $request->input('semester_id'))],
            'rows.*.lecturer_id' => ['required', 'exists:lecturers,id'],
            'rows.*.note' => ['nullable', 'string', 'max:150'],
        ], [
            'course_id.required' => 'Choose a course.',
            'course_id.exists' => 'Choose a course the campus offers.',
            'level_id.required' => 'Choose a level.',
            'level_id.exists' => 'Choose a level of this course.',
            'semester_id.required' => 'Choose a semester.',
            'semester_id.exists' => 'Choose a semester of this course and level.',
            'rows.*.day.in' => 'Choose a valid day for every class.',
            'rows.*.lecturer_id.exists' => 'A chosen lecturer was not found.',
            'rows.required' => 'Add at least one class to the timetable.',
            'rows.min' => 'Add at least one class to the timetable.',
            'rows.*.day.required' => 'Choose a day for every class.',
            'rows.*.start_time.required' => 'Enter a start time for every class.',
            'rows.*.end_time.required' => 'Enter an end time for every class.',
            'rows.*.subject_id.required' => 'Choose a module for every class.',
            'rows.*.subject_id.exists' => 'A chosen module does not belong to this course, level and semester.',
            'rows.*.lecturer_id.required' => 'Choose a lecturer for every class.',
        ]);

        foreach ($data['rows'] as $i => $row) {
            if ($row['end_time'] <= $row['start_time']) {
                return back()->withInput()->withErrors(['rows' => 'In class '.($i + 1).', the end time must be after the start time.']);
            }
        }

        $timetable = Timetable::where($request->only('course_id', 'level_id', 'semester_id'))->first();
        $isNew = ! $timetable;
        $before = $timetable ? $timetable->rows()->pluck('lecturer_id')->all() : [];

        DB::transaction(function () use (&$timetable, $data) {
            $timetable = Timetable::updateOrCreate(
                ['course_id' => $data['course_id'], 'level_id' => $data['level_id'], 'semester_id' => $data['semester_id']],
                ['batch' => $data['batch'] ?? null, 'mode_note' => $data['mode_note'] ?? null]
            );
            $timetable->rows()->delete();
            foreach ($data['rows'] as $row) {
                $timetable->rows()->create([
                    'day' => $row['day'],
                    'start_time' => $row['start_time'],
                    'end_time' => $row['end_time'],
                    'subject_id' => $row['subject_id'],
                    'lecturer_id' => $row['lecturer_id'],
                    'note' => $row['note'] ?? null,
                ]);
            }
        });

        $this->notifyLecturers($timetable->fresh(['course', 'level', 'semester']), $before, $isNew);

        return redirect()->route('admin.timetables.index')
            ->with('success', $isNew ? 'Timetable saved.' : 'Timetable updated.');
    }

    /** Reads a timetable picture and returns rows for the form (JSON). Nothing is saved or stored. */
    public function readImage(Request $request, TimetableReader $reader)
    {
        $request->validate([
            'course_id' => ['required', Rule::exists('courses', 'id')->where('status', 'active')],
            'level_id' => ['required', Rule::exists('levels', 'id')->where('course_id', $request->input('course_id'))],
            'semester_id' => ['required', Rule::exists('semesters', 'id')
                ->where('course_id', $request->input('course_id'))->where('level_id', $request->input('level_id'))],
            'image' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ], [
            'image.required' => 'Choose a picture of the timetable.',
            'image.mimes' => 'The picture must be a JPG, PNG or WEBP file.',
            'image.max' => 'The picture is too big (4 MB at most). Use a smaller picture.',
            'image.uploaded' => 'The picture could not be uploaded. Try a smaller one.',
        ]);

        $modules = Subject::where('course_id', $request->course_id)->where('level_id', $request->level_id)
            ->where('semester_id', $request->semester_id)->orderBy('name')->get(['id', 'code', 'name'])
            ->map(fn ($m) => ['id' => $m->id, 'code' => (string) $m->code, 'name' => (string) $m->name])->all();
        $lecturers = Lecturer::orderBy('name')->get(['id', 'name'])->map(fn ($l) => ['id' => $l->id, 'name' => (string) $l->name])->all();

        if (! $modules) {
            return response()->json(['message' => 'This semester has no modules yet. Add them first on the Faculties page.'], 422);
        }

        try {
            return response()->json($reader->read($request->file('image'), $modules, $lecturers));
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            report($e);

            return response()->json(['message' => 'Something went wrong while reading the picture. Try again, or fill the classes by hand.'], 500);
        }
    }

    public function destroy(Timetable $timetable)
    {
        $before = $timetable->rows()->pluck('lecturer_id')->all();
        $timetable->load(['course', 'level', 'semester']);
        $timetable->delete();

        $this->notifyLecturers($timetable, $before, false, 'removed');

        return redirect()->route('admin.timetables.index')->with('success', 'Timetable deleted.');
    }

    /** Lecturers in the new timetable (and those taken out of it) get a notification. */
    private function notifyLecturers(Timetable $timetable, array $before, bool $isNew, ?string $action = null): void
    {
        $now = $action === 'removed' ? [] : $timetable->rows()->pluck('lecturer_id')->all();
        $label = collect([$timetable->course->name ?? null, $timetable->level->name ?? null, $timetable->semester->name ?? null])->filter()->implode(' / ');

        foreach (array_unique(array_merge($before, $now)) as $id) {
            if ($lecturer = Lecturer::find($id)) {
                $lecturer->notify(new TimetableAssigned($label, $isNew && in_array($id, $now) ? 'assigned' : 'updated'));
            }
        }
    }
}
