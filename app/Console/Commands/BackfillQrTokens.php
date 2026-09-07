<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Student;
use App\Models\Lecturer;
use App\Models\GateDevice;

class BackfillQrTokens extends Command
{
    protected $signature = 'gate-log:backfill-tokens';
    protected $description = 'Generate qr_token for students/lecturers that don\'t have one yet';

    public function handle()
    {
        $students = Student::whereNull('qr_token')->get();
        foreach ($students as $student) {
            $student->update(['qr_token' => GateDevice::generateKey()]);
        }
        $this->info("Students updated: {$students->count()}");

        $lecturers = Lecturer::whereNull('qr_token')->get();
        foreach ($lecturers as $lecturer) {
            $lecturer->update(['qr_token' => GateDevice::generateKey()]);
        }
        $this->info("Lecturers updated: {$lecturers->count()}");

        $this->info('Backfill complete.');
    }
}