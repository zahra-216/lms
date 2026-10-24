<?php

namespace App\Support;

use App\Models\Semester;
use App\Models\Student;
use App\Models\Subject;

/**
 * What a student may open: the subjects of their own course and level, from semester 1 up to
 * their current semester. The admin moves a student on by changing their semester.
 * (Same rule the dashboard uses to decide which semesters to list.)
 */
class StudentSubjectAccess
{
    /** "Semester 3" -> 3 (null when the student has no current semester set) */
    public static function currentNumber(Student $student): ?int
    {
        $current = $student->semester_id ? Semester::find($student->semester_id) : null;

        return $current ? self::numberOf($current->name) : null;
    }

    public static function numberOf(?string $semesterName): int
    {
        return (int) filter_var((string) $semesterName, FILTER_SANITIZE_NUMBER_INT);
    }

    public static function canOpenSemester(Student $student, Semester $semester): bool
    {
        if ((int) $semester->course_id !== (int) $student->course_id || (int) $semester->level_id !== (int) $student->level_id) {
            return false;
        }

        $current = self::currentNumber($student);

        return ! $current || self::numberOf($semester->name) <= $current;
    }

    public static function canOpenSubject(Student $student, Subject $subject): bool
    {
        if ((int) $subject->course_id !== (int) $student->course_id || (int) $subject->level_id !== (int) $student->level_id) {
            return false;
        }

        $semester = $subject->semester_id ? Semester::find($subject->semester_id) : null;
        $current = self::currentNumber($student);

        return ! $current || ! $semester || self::numberOf($semester->name) <= $current;
    }
}
