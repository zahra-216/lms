<?php

namespace App\Exports;

use App\Models\Enrollment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EnrollmentsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Enrollment::with(['student', 'course'])->get()->map(function($enrollment){
            return [
                'Student' => $enrollment->student->name,
                'Course' => $enrollment->course->name,
                'Status' => $enrollment->status,
                'Enrolled At' => $enrollment->enrolled_at,
                'Completed At' => $enrollment->completed_at,
            ];
        });
    }

    public function headings(): array
    {
        return ['Student', 'Course', 'Status', 'Enrolled At', 'Completed At'];
    }
}