<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\StudentController;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class StudentRegistrationValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_store_allows_any_registration_number_format(): void
    {
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('PDO SQLite extension is not available in this environment.');
        }

        $controller = new StudentController();

        $request = Request::create('/admin/students', 'POST', [
            'registration_no' => 'abc123',
            'name' => 'Test Student',
            'email' => 'student@example.com',
            'branch' => 'CSE',
            'course_id' => 1,
            'level_id' => 1,
            'password' => 'password1234567',
        ]);

        $response = $controller->store($request);

        $this->assertTrue($response->isRedirect());
        $this->assertDatabaseHas('students', [
            'registration_no' => 'ABC123',
        ]);
        $this->assertCount(1, Student::all());
    }
}
