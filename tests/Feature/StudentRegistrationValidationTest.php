<?php

namespace Tests\Feature;

use Tests\TestCase;

class StudentRegistrationValidationTest extends TestCase
{
    public function test_student_store_validation_no_longer_uses_registration_number_regex(): void
    {
        $controllerSource = file_get_contents(base_path('app/Http/Controllers/Admin/StudentController.php'));

        $this->assertStringNotContainsString('regex:/^TTMC', $controllerSource);
        $this->assertStringNotContainsString('registration_no.regex', $controllerSource);
    }
}
