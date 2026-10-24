<?php

namespace App\Support;

use App\Models\Course;
use App\Models\Faculty;
use App\Models\Level;
use App\Models\Semester;
use App\Models\Subject;
use Illuminate\Http\Request;

/**
 * Drill-down used by Attendance, Assignments and Marks:
 * faculty -> course -> level -> semester -> subject, one set of cards per step.
 * The chosen path lives in the address (?faculty=1&course=2&...), so Back and bookmarks work.
 */
class AcademicTree
{
    /**
     * @param  callable(Subject): string  $leafUrl  where a subject card leads
     * @return array{crumbs: array, heading: string, cards: array, searching: bool, q: string}
     */
    public static function build(Request $request, callable $leafUrl): array
    {
        $q = trim((string) $request->query('q'));
        $path = $request->only(['faculty', 'course', 'level', 'semester']);
        $base = $request->url();
        $url = fn (array $extra) => $base.'?'.http_build_query($extra);

        // ---- search across every subject ----
        if ($q !== '') {
            $subjects = Subject::with(['course', 'level', 'semester'])
                ->where(fn ($w) => $w->where('name', 'like', "%{$q}%")->orWhere('code', 'like', "%{$q}%"))
                ->orderBy('code')->limit(60)->get();

            return [
                'crumbs' => [['All faculties', $base]],
                'heading' => 'Subjects matching "'.$q.'"',
                'cards' => $subjects->map(fn ($s) => self::subjectCard($s, $leafUrl))->all(),
                'searching' => true,
                'q' => $q,
            ];
        }

        $crumbs = [['All faculties', $base]];
        $faculty = isset($path['faculty']) ? Faculty::find($path['faculty']) : null;
        $course = $faculty && isset($path['course']) ? Course::where('faculty_id', $faculty->id)->find($path['course']) : null;
        $level = $course && isset($path['level']) ? Level::where('course_id', $course->id)->find($path['level']) : null;
        $semester = $level && isset($path['semester']) ? Semester::where('level_id', $level->id)->find($path['semester']) : null;

        if ($faculty) {
            $crumbs[] = [$faculty->name, $url(['faculty' => $faculty->id])];
        }
        if ($course) {
            $crumbs[] = [$course->name, $url(['faculty' => $faculty->id, 'course' => $course->id])];
        }
        if ($level) {
            $crumbs[] = [$level->name, $url(['faculty' => $faculty->id, 'course' => $course->id, 'level' => $level->id])];
        }
        if ($semester) {
            $crumbs[] = [$semester->name, null];
        }

        if ($semester) {
            $cards = Subject::where('semester_id', $semester->id)->orderBy('code')->get()
                ->map(fn ($s) => self::subjectCard($s->setRelation('course', $course)->setRelation('level', $level)->setRelation('semester', $semester), $leafUrl))->all();
            $heading = 'Choose a subject';
        } elseif ($level) {
            $cards = Semester::where('level_id', $level->id)->withCount('subjects')->get()
                ->sortBy(fn ($s) => (int) preg_replace('/\D/', '', $s->name))->values()
                ->map(fn ($s) => [
                    'title' => $s->name, 'sub' => $s->subjects_count.' '.str('subject')->plural($s->subjects_count), 'icon' => 'calendar3', 'img' => null,
                    'url' => $url(['faculty' => $faculty->id, 'course' => $course->id, 'level' => $level->id, 'semester' => $s->id]),
                ])->all();
            $heading = 'Choose a semester';
        } elseif ($course) {
            $cards = Level::where('course_id', $course->id)->withCount('semesters')->get()
                ->map(fn ($l) => [
                    'title' => $l->name, 'sub' => $l->semesters_count.' '.str('semester')->plural($l->semesters_count), 'icon' => 'layers', 'img' => null,
                    'url' => $url(['faculty' => $faculty->id, 'course' => $course->id, 'level' => $l->id]),
                ])->all();
            $heading = 'Choose a level';
        } elseif ($faculty) {
            $cards = Course::with('faculty')->where('faculty_id', $faculty->id)->withCount('levels')->orderBy('name')->get()
                ->map(fn ($c) => [
                    'title' => $c->name, 'sub' => trim($c->code.' · '.$c->levels_count.' '.str('level')->plural($c->levels_count), ' ·'), 'icon' => 'mortarboard',
                    'img' => StockImages::forCourse($c), 'url' => $url(['faculty' => $faculty->id, 'course' => $c->id]),
                ])->all();
            $heading = 'Choose a course';
        } else {
            $cards = Faculty::withCount('courses')->orderBy('name')->get()
                ->map(fn ($f) => [
                    'title' => $f->name, 'sub' => $f->courses_count.' '.str('course')->plural($f->courses_count), 'icon' => 'building',
                    'img' => StockImages::forFaculty($f), 'url' => $url(['faculty' => $f->id]),
                ])->all();
            $heading = 'Choose a faculty';
        }

        return ['crumbs' => $crumbs, 'heading' => $heading, 'cards' => $cards, 'searching' => false, 'q' => ''];
    }

    private static function subjectCard(Subject $s, callable $leafUrl): array
    {
        return [
            'title' => $s->code.' - '.$s->name,
            'sub' => collect([$s->course->name ?? null, $s->level->name ?? null, $s->semester->name ?? null])->filter()->implode(' · '),
            'icon' => 'book', 'img' => null, 'url' => $leafUrl($s),
        ];
    }
}
