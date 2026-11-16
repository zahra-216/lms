<?php

namespace App\Console\Commands;

use App\Models\Course;
use App\Models\Student;
use Illuminate\Console\Command;

/**
 * Keeps only the courses the campus offers now as "active" (the same list as the website's programmes).
 * Nothing is deleted: other courses become "inactive", so their data and students stay, and they can be switched
 * back on later. Inactive courses are left out of the timetable form, new-student forms and the course browsers.
 */
class SetActiveCourses extends Command
{
    protected $signature = 'courses:set-active {--dry-run : Show what would change, change nothing}';

    protected $description = 'Mark only the courses the campus offers now as active, and the rest as inactive';

    /** Course names (lower case, "&" written as "and") that the campus offers. */
    private const OFFERED = [
        'english',
        'digital marketing',
        'artificial intelligence',
        'cyber security',
        'mobile development',
        'business management',
        'data science',
        'computer science',
        'human resources management',
    ];

    /** The levels offered for each course (same names as the website). A level with students is never switched off. */
    private const OFFERED_LEVELS = [
        'english' => ['Diploma', 'HND', 'Degree'],
        'digital marketing' => ['Diploma'],
        'artificial intelligence' => ['Diploma', 'HND', 'Degree'],
        'cyber security' => ['Diploma', 'HND', 'Degree'],
        'mobile development' => ['Diploma'],
        'business management' => ['HND', 'Degree'],
        'data science' => ['HND', 'Degree'],
        'computer science' => ['HND', 'Degree'],
        'human resources management' => ['Degree'],
        'information technology' => ['Diploma', 'HND', 'Degree'],
    ];

    /** Offered programmes that have no course yet if none of these words appear in a course name. */
    private const EXPECTED_WORDS = [
        'Information Technology (Diploma, HND, BSc and BIT)' => 'information technology',
        'Full Stack Development (Diploma)' => 'full stack',
    ];

    private function normal(string $name): string
    {
        return preg_replace('/\s+/', ' ', trim(str_replace('&', 'and', strtolower($name))));
    }

    private function offered(string $name): bool
    {
        $n = $this->normal($name);

        return in_array($n, self::OFFERED, true)
            || str_contains($n, 'information technology')
            || str_contains($n, 'full stack');
    }

    /** Switch off the levels a course does not offer, except levels that have students. */
    private function levels(bool $dry): void
    {
        $rows = [];
        $changes = 0;

        foreach (Course::orderBy('name')->get() as $course) {
            $offered = self::OFFERED_LEVELS[$this->normal($course->name)] ?? null;
            if ($offered === null) {
                continue; // not an offered course (or an IT / Full Stack course added later): levels left as they are
            }

            $names = $course->levels()->pluck('name')->all();
            foreach ($offered as $want) {
                if (! in_array($want, $names, true)) {
                    $this->warn("{$course->name}: {$want} is offered but the course has no {$want} level yet (add it when needed).");
                }
            }

            foreach ($course->levels as $level) {
                $students = Student::where('level_id', $level->id)->count();
                $isOffered = in_array($level->name, $offered, true);
                $want = $isOffered || $students > 0;
                $note = $isOffered ? 'offered' : ($students > 0 ? "NOT offered, kept because of {$students} student(s)" : 'not offered');

                if ((bool) $level->is_active !== $want) {
                    $changes++;
                    if (! $dry) {
                        $level->update(['is_active' => $want]);
                    }
                }

                if (! $isOffered) {
                    $rows[] = [$course->name, $level->name, $want ? 'visible' : 'hidden', $note];
                }
            }
        }

        $this->newLine();
        $this->table(['Course', 'Level not on the list', 'Result', 'Why'], $rows);
        $this->info(($dry ? 'Dry run: ' : '').$changes.' level(s) '.($dry ? 'would change.' : 'changed.'));
    }

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');
        $rows = [];
        $changes = 0;

        foreach (Course::orderBy('name')->get() as $course) {
            $students = Student::where('course_id', $course->id)->count();
            $offered = $this->offered($course->name);
            // a course that has students is never switched off, even if it is not on the list
            $want = ($offered || $students > 0) ? 'active' : 'inactive';
            $change = $course->status !== $want && ! ($course->status === 'archived' && $want === 'inactive');

            $becomes = $change ? $want : '(same)';
            if (! $offered && $students > 0) {
                $becomes .= ' - not on the list, kept for its student(s)';
            }
            $rows[] = [$course->id, $course->name, $course->status, $becomes, $students];

            if ($change) {
                $changes++;
                if (! $dry) {
                    $course->update(['status' => $want]);
                }
            }
        }

        $this->table(['ID', 'Course', 'Now', 'Becomes', 'Students'], $rows);

        $this->levels($dry);

        foreach (self::EXPECTED_WORDS as $label => $word) {
            if (! Course::get()->contains(fn ($c) => str_contains($this->normal($c->name), $word))) {
                $this->warn("No course for: {$label}. Add it on the Courses page (then its levels, semesters and modules).");
            }
        }

        $this->info(($dry ? 'Dry run: ' : '').$changes.' course(s) '.($dry ? 'would change.' : 'changed.'));

        return self::SUCCESS;
    }
}
