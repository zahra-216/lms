<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

use App\Http\Controllers\FrontendController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LecturerAuthController;
use App\Http\Controllers\Student\StudentController as StudentStudentController;
use App\Http\Controllers\Student\SubjectController as StudentSubjectController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;

use App\Http\Controllers\Admin\{
    AuthController,
    ProfileController,
    StudentController,
    LecturerController,
    FacultyController,
    CourseController,
    LevelController,
    SemesterController,
    SubjectController,
    NoteController,
    EnrollmentController,
    AssignmentController,
    SubmissionController
};

use App\Http\Controllers\Admin\AjaxController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\MarkController;

/*
|--------------------------------------------------------------------------
| Public / Frontend Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [FrontendController::class, 'home'])->name('home');

Route::get('/faculty/{facultyId}/courses', [FrontendController::class, 'facultyCourses'])
    ->name('faculty.courses');

Route::get('/course/{courseId}/levels', [FrontendController::class, 'courseLevels']);

Route::get('/login', [FrontendController::class, 'loginPage'])->name('login');
Route::post('/login', [FrontendController::class, 'login'])->name('login.submit');

Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

Route::get('/student-grade', function () {
    return view('student-grade');
});

Route::get('/verify', [App\Http\Controllers\VerifyController::class, 'show'])->name('verify.show');
Route::post('/verify', [App\Http\Controllers\VerifyController::class, 'check'])->name('verify.check');


/*
|--------------------------------------------------------------------------
| Lecturer Routes
|--------------------------------------------------------------------------
*/

Route::prefix('lecturer')->group(function () {

    Route::get('/login', [LecturerAuthController::class, 'showLoginForm'])->name('lecturer.login');
    Route::post('/login', [LecturerAuthController::class, 'login'])->name('lecturer.login.submit');
    Route::post('/logout', [LecturerAuthController::class, 'logout'])->name('lecturer.logout');

    Route::middleware('auth:lecturer')->group(function () {

        Route::get('/dashboard', [LecturerAuthController::class, 'dashboard'])
            ->name('lecturer.dashboard');

        Route::name('lecturer.')->group(function () {

            Route::get('/my-payments', [App\Http\Controllers\LecturerAuthController::class, 'myPayments'])
                ->name('my.payments');

            Route::get('/subject/{id}', [App\Http\Controllers\LecturerSubjectController::class, 'show'])
                ->name('subject.show');

            Route::get('/subject/{id}/notes', [App\Http\Controllers\LecturerSubjectController::class, 'notes'])
                ->name('subject.notes');

            Route::get('/subject/{id}/videos', [App\Http\Controllers\LecturerSubjectController::class, 'videos'])
                ->name('subject.videos');

            Route::get('/subject/{id}/assignments', [App\Http\Controllers\LecturerSubjectController::class, 'assignments'])
                ->name('subject.assignments');

            Route::get('/subject/{id}/grades', [App\Http\Controllers\LecturerSubjectController::class, 'grades'])
                ->name('subject.grades');

            Route::get('/subject/{id}/timetable', [App\Http\Controllers\LecturerSubjectController::class, 'timetable'])
                ->name('subject.timetable');

            Route::post('/notification/read/{id}', function ($id) {
                auth('lecturer')->user()->notifications()->where('id', $id)->update(['read_at' => now()]);
                return response()->json(['success' => true]);
            })->name('notification.read');

            Route::post('/notification/read-all', function () {
                auth('lecturer')->user()->unreadNotifications->markAsRead();
                return response()->json(['success' => true]);
            })->name('notification.readAll');

            Route::get('/assignment/{assignment}/marks/create', [App\Http\Controllers\Admin\MarkController::class, 'lecturerCreate'])
                ->name('marks.create');

            Route::post('/marks/store', [App\Http\Controllers\Admin\MarkController::class, 'lecturerStore'])
                ->name('marks.store');

            Route::post('/subject/{id}/marks/update', [App\Http\Controllers\LecturerSubjectController::class, 'updateMarks'])
                ->name('subject.marks.update');

            // Notes
            Route::get('/subject/{subject}/notes/create', [App\Http\Controllers\LecturerNoteController::class, 'create'])
                ->name('notes.create');
            Route::post('/subject/{subject}/notes', [App\Http\Controllers\LecturerNoteController::class, 'store'])
                ->name('notes.store');
            Route::get('/subject/{subject}/notes/{note}/edit', [App\Http\Controllers\LecturerNoteController::class, 'edit'])
                ->name('notes.edit');
            Route::put('/subject/{subject}/notes/{note}', [App\Http\Controllers\LecturerNoteController::class, 'update'])
                ->name('notes.update');
            Route::delete('/subject/{subject}/notes/{note}', [App\Http\Controllers\LecturerNoteController::class, 'destroy'])
                ->name('notes.destroy');
            Route::get('/notes/{note}/download', [App\Http\Controllers\LecturerNoteController::class, 'download'])
                ->name('notes.download');

            // Videos
            Route::get('/subject/{subject}/videos/create', [App\Http\Controllers\LecturerVideoController::class, 'create'])
                ->name('videos.create');
            Route::post('/subject/{subject}/videos', [App\Http\Controllers\LecturerVideoController::class, 'store'])
                ->name('videos.store');
            Route::get('/subject/{subject}/videos/{video}/edit', [App\Http\Controllers\LecturerVideoController::class, 'edit'])
                ->name('videos.edit');
            Route::put('/subject/{subject}/videos/{video}', [App\Http\Controllers\LecturerVideoController::class, 'update'])
                ->name('videos.update');
            Route::delete('/subject/{subject}/videos/{video}', [App\Http\Controllers\LecturerVideoController::class, 'destroy'])
                ->name('videos.destroy');

            // Assignment creation
            Route::get('/subject/{subject}/assignments/create', [App\Http\Controllers\LecturerAssignmentController::class, 'create'])
                ->name('assignments.create');
            Route::post('/subject/{subject}/assignments', [App\Http\Controllers\LecturerAssignmentController::class, 'store'])
                ->name('assignments.store');
            Route::get('/subject/{subject}/assignments/{assignment}/edit', [App\Http\Controllers\LecturerAssignmentController::class, 'edit'])
                ->name('assignments.edit');
            Route::put('/subject/{subject}/assignments/{assignment}', [App\Http\Controllers\LecturerAssignmentController::class, 'update'])
                ->name('assignments.update');
            Route::delete('/subject/{subject}/assignments/{assignment}', [App\Http\Controllers\LecturerAssignmentController::class, 'destroy'])
                ->name('assignments.destroy');

            // Lecture Records — read-only history + add content
            Route::get('/lecture-records', [App\Http\Controllers\LecturerLectureRecordController::class, 'index'])
                ->name('lecture-records.index');

            Route::get('/lecture-records/by-month', [App\Http\Controllers\LecturerLectureRecordController::class, 'byMonth'])
                ->name('lecture-records.by-month');

            Route::get('/lecture-records/pdf', [App\Http\Controllers\LecturerLectureRecordController::class, 'pdf'])
                ->name('lecture-records.pdf');

            Route::get('/lecture-records/create', [App\Http\Controllers\LecturerLectureRecordController::class, 'create'])
                ->name('lecture-records.create');
            Route::post('/lecture-records', [App\Http\Controllers\LecturerLectureRecordController::class, 'store'])
                ->name('lecture-records.store');

            Route::get('/lecture-record/{record}/add-content', [App\Http\Controllers\LecturerLectureRecordController::class, 'addContentForm'])
                ->name('lecture-records.add-content');
            Route::post('/lecture-record/{record}/add-content', [App\Http\Controllers\LecturerLectureRecordController::class, 'addContentStore'])
                ->name('lecture-records.add-content.store');
        });
    });
});


/*
|--------------------------------------------------------------------------
| Student Routes
|--------------------------------------------------------------------------
*/

Route::prefix('student')->name('student.')->middleware('student.auth')->group(function () {

    Route::get('/grades', [StudentStudentController::class, 'grades'])
        ->name('grades');

    Route::get('/subject/{id}/grades', [StudentStudentController::class, 'subjectGrades'])
        ->name('subject.grades');

    Route::get('/my-payments', [StudentStudentController::class, 'myPayments'])
        ->name('my.payments');

    Route::get('/subject/{id}/show', [App\Http\Controllers\Student\StudentSubjectPortalController::class, 'show'])
        ->name('subject.portal.show');

    Route::get('/subject/{id}/portal-notes', [App\Http\Controllers\Student\StudentSubjectPortalController::class, 'notes'])
        ->name('subject.portal.notes');

    Route::get('/subject/{id}/portal-videos', [App\Http\Controllers\Student\StudentSubjectPortalController::class, 'videos'])
        ->name('subject.portal.videos');

    Route::get('/subject/{id}/portal-assignments', [App\Http\Controllers\Student\StudentSubjectPortalController::class, 'assignments'])
        ->name('subject.portal.assignments');

    Route::get('/subject/{id}/portal-grades', [App\Http\Controllers\Student\StudentSubjectPortalController::class, 'grades'])
        ->name('subject.portal.grades');

    Route::get('/profile/edit', [App\Http\Controllers\Student\ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::post('/profile/update', [App\Http\Controllers\Student\ProfileController::class, 'update'])
        ->name('profile.update');

    Route::get('/subject/{id}/assignments', [StudentSubjectController::class, 'assignments'])
        ->name('subject.assignments');

    Route::post('/verify-subject', [StudentSubjectController::class, 'verifySubject']);

    Route::get('/semester/{id}/subjects', [SemesterController::class, 'getSubjects']);

    Route::get('/subject/{id}/notes', [StudentSubjectController::class, 'notes']);

    Route::get('/download/{id}', [StudentSubjectController::class, 'download'])
        ->name('note.download');

    Route::get('/assignment/download/{id}', [SubjectController::class, 'downloadAssignment'])
        ->name('assignment.download');

    Route::get('/profile', function () {
        $student = \App\Models\Student::find(session('student_id'));

        if (!$student) {
            return redirect()->route('login');
        }

        return view('student.profile', compact('student'));
    })->name('profile');

    Route::post('/photo-update', [App\Http\Controllers\Admin\StudentController::class, 'updatePhoto'])
        ->name('photo.update');

    Route::post('/notification/read/{id}', function ($id) {
        $studentId = session('student_id');
        $student = \App\Models\Student::find($studentId);

        if ($student) {
            $student->notifications()->where('id', $id)->update(['read_at' => now()]);
        }

        return response()->json(['success' => true]);
    })->name('notification.read');

    Route::post('/notification/read-all', function () {
        $studentId = session('student_id');
        $student = \App\Models\Student::find($studentId);

        if ($student) {
            $student->unreadNotifications->markAsRead();
        }

        return response()->json(['success' => true]);
    })->name('notification.readAll');
});

// Not student-prefixed in URL — left top-level to preserve exact path
Route::post('/assignment/submit', [AssignmentController::class, 'submit'])
    ->name('assignment.submit')
    ->middleware('student.auth');
Route::put('/assignment/submission/{submission}', [AssignmentController::class, 'updateSubmission'])
    ->name('assignment.submission.update')
    ->middleware('student.auth');
Route::delete('/assignment/submission/{submission}', [AssignmentController::class, 'destroySubmission'])
    ->name('assignment.submission.destroy')
    ->middleware('student.auth');

/*
|--------------------------------------------------------------------------
| Admin Routes — Public (no auth middleware)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->group(function () {

    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login'])->name('admin.login.submit');

    Route::get('/forgot-password', [AdminController::class, 'forgotForm'])
        ->name('admin.password.request');
    Route::post('/forgot-password', [AdminController::class, 'sendResetLink'])
        ->name('admin.password.email');
    Route::get('/reset-password/{token}', [AdminController::class, 'resetForm'])
        ->name('admin.password.reset');
    Route::post('/reset-password', [AdminController::class, 'resetPassword'])
        ->name('admin.password.update');
});

/*
|--------------------------------------------------------------------------
| Admin Routes — Protected (auth:admin)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->middleware(['auth:admin'])->name('admin.')->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/create-admin', [AdminController::class, 'create'])->name('create');
    Route::post('/create-admin', [AdminController::class, 'store'])->name('store');

    Route::post('/logout', function () {
        Auth::guard('admin')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('admin.login');
    })->name('logout');

    Route::resources([
        'students' => StudentController::class,
        'lecturers' => LecturerController::class,
        'faculties' => FacultyController::class,
        'courses' => CourseController::class,
        'levels' => LevelController::class,
        'semesters' => SemesterController::class,
        'subjects' => SubjectController::class,
    ]);

    Route::get('/assignments', [AssignmentController::class, 'index'])
        ->name('assignments.index');

    Route::get('/assignments/browse', [AssignmentController::class, 'browse'])
        ->name('assignments.browse');

    Route::get('/assignments/{id}/submissions', [AssignmentController::class, 'submissions'])
        ->name('assignments.submissions');

    Route::get('/get-subjects', [AjaxController::class, 'getSubjects']);
    Route::get('/get-notes/{subject_id}', [AjaxController::class, 'getNotes']);

    // Marks
    Route::get('/marks/{assignment_id}', [MarkController::class, 'create'])
        ->name('marks.create');
    Route::post('/marks/store', [MarkController::class, 'store'])
        ->name('marks.store');
    Route::get('/marks', [MarkController::class, 'index'])
        ->name('marks.index');

    Route::get('/faculties/{faculty}/courses', [FacultyController::class, 'courses'])
        ->name('faculties.courses');

    Route::get('/get-levels/{courseId}', [LevelController::class, 'getByCourse'])
        ->name('levels.byCourse');

    Route::get('/get-semesters/{levelId}', [SemesterController::class, 'getByLevel'])
        ->name('semesters.byLevel');

    Route::get('/semester/{id}/subjects', [SemesterController::class, 'getSubjects'])
        ->name('semester.subjects');

    // Enrollments (pending removal)
    Route::get('enrollments/export/pdf', [EnrollmentController::class, 'exportPdf'])
        ->name('enrollments.pdf');
    Route::get('enrollments', [EnrollmentController::class, 'index'])
        ->name('enrollments.index');
    Route::get('enrollments/create', [EnrollmentController::class, 'create'])
        ->name('enrollments.create');
    Route::post('enrollments', [EnrollmentController::class, 'store'])
        ->name('enrollments.store');
    Route::get('enrollments/{id}/edit', [EnrollmentController::class, 'edit'])
        ->name('enrollments.edit');
    Route::put('enrollments/{id}', [EnrollmentController::class, 'update'])
        ->name('enrollments.update');
    Route::delete('enrollments/{id}', [EnrollmentController::class, 'destroy'])
        ->name('enrollments.delete');

    // Notes / Assignments / Grades (nested under subjects/{subject})
    Route::prefix('subjects/{subject}')->name('subjects.')->group(function () {
        Route::get('notes', [NoteController::class, 'index'])->name('notes.index');
        Route::get('notes/create', [NoteController::class, 'create'])->name('notes.create');
        Route::post('notes', [NoteController::class, 'store'])->name('notes.store');
        Route::get('notes/{note}/edit', [NoteController::class, 'edit'])->name('notes.edit');
        Route::put('notes/{note}', [NoteController::class, 'update'])->name('notes.update');
        Route::delete('notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');
        Route::get('notes/{note}/download', [NoteController::class, 'download'])->name('notes.download');

        Route::get('assignments', [AssignmentController::class, 'subjectIndex'])->name('assignments.index');
        Route::get('assignments/create', [AssignmentController::class, 'create'])->name('assignments.create');
        Route::post('assignments', [AssignmentController::class, 'store'])->name('assignments.store');
        Route::get('assignments/{assignment}/edit', [AssignmentController::class, 'edit'])->name('assignments.edit');
        Route::put('assignments/{assignment}', [AssignmentController::class, 'update'])->name('assignments.update');
        Route::delete('assignments/{assignment}', [AssignmentController::class, 'destroy'])->name('assignments.destroy');

        Route::get('grades', [SubjectController::class, 'grades'])->name('grades');
        Route::post('grades/update', [SubjectController::class, 'updateMarks'])->name('grades.update');

        // Timetable
        Route::get('timetables', [App\Http\Controllers\Admin\TimetableController::class, 'index'])->name('timetables.index');
        Route::get('timetables/create', [App\Http\Controllers\Admin\TimetableController::class, 'create'])->name('timetables.create');
        Route::post('timetables', [App\Http\Controllers\Admin\TimetableController::class, 'store'])->name('timetables.store');
        Route::get('timetables/{groupId}/edit', [App\Http\Controllers\Admin\TimetableController::class, 'edit'])->name('timetables.edit');
        Route::put('timetables/{groupId}', [App\Http\Controllers\Admin\TimetableController::class, 'update'])->name('timetables.update');
        Route::delete('timetables/{groupId}', [App\Http\Controllers\Admin\TimetableController::class, 'destroy'])->name('timetables.destroy');
    });

    Route::get('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'show'])
        ->name('profile');
    Route::post('/profile/update', [App\Http\Controllers\Admin\ProfileController::class, 'update'])
        ->name('profile.update');

    Route::get('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'index'])
        ->name('settings');
    Route::post('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'update'])
        ->name('settings.update');

    Route::post('/notification/read/{id}', function ($id) {
        auth('admin')->user()->notifications()->where('id', $id)->update(['read_at' => now()]);
        return response()->json(['success' => true]);
    })->name('notification.read');

    Route::post('/notification/read-all', function () {
        auth('admin')->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    })->name('notification.readAll');

    // Attendance
    Route::get('/attendance', [App\Http\Controllers\Admin\AttendanceController::class, 'index'])
        ->name('attendance.index');
    Route::get('/attendance/{id}', [App\Http\Controllers\Admin\AttendanceController::class, 'show'])
        ->name('attendance.show');
    Route::get('/attendance/{id}/mark', [App\Http\Controllers\Admin\AttendanceController::class, 'mark'])
        ->name('attendance.mark');
    Route::post('/attendance/{id}/mark', [App\Http\Controllers\Admin\AttendanceController::class, 'markStore'])
        ->name('attendance.mark.store');
    Route::get('/attendance/{id}/history', [App\Http\Controllers\Admin\AttendanceController::class, 'history'])
        ->name('attendance.history');
    Route::get('/attendance/{id}/history/{month}/pdf', [App\Http\Controllers\Admin\AttendanceController::class, 'monthlyPdf'])
        ->name('attendance.monthly.pdf');
    Route::delete('/attendance/{id}/history/{month}', [App\Http\Controllers\Admin\AttendanceController::class, 'deleteMonth'])
        ->name('attendance.monthly.destroy');

    // Lecture Records
    Route::get('/lecture-records', [App\Http\Controllers\Admin\LectureRecordController::class, 'index'])
        ->name('lecture-records.index');
    Route::delete('/lecture-records/{ids}', [App\Http\Controllers\Admin\LectureRecordController::class, 'destroy'])
        ->name('lecture-records.destroy')
        ->where('ids', '[0-9,]+');
    Route::get('/lecture-records/create', [App\Http\Controllers\Admin\LectureRecordController::class, 'create'])
        ->name('lecture-records.create');
    Route::post('/lecture-records', [App\Http\Controllers\Admin\LectureRecordController::class, 'store'])
        ->name('lecture-records.store');

    Route::get('/lecture-records/get-courses', [App\Http\Controllers\Admin\LectureRecordController::class, 'getCourses'])
    ->name('lecture-records.get-courses');
    Route::get('/lecture-records/get-levels', [App\Http\Controllers\Admin\LectureRecordController::class, 'getLevels'])
        ->name('lecture-records.get-levels');
    Route::get('/lecture-records/get-semesters', [App\Http\Controllers\Admin\LectureRecordController::class, 'getSemesters'])
        ->name('lecture-records.get-semesters');
    Route::get('/lecture-records/get-subjects', [App\Http\Controllers\Admin\LectureRecordController::class, 'getSubjects'])
        ->name('lecture-records.get-subjects');
    Route::get('/lecture-records/{ids}/edit', [App\Http\Controllers\Admin\LectureRecordController::class, 'edit'])
        ->name('lecture-records.edit')
        ->where('ids', '[0-9,]+');
    Route::put('/lecture-records/{ids}', [App\Http\Controllers\Admin\LectureRecordController::class, 'update'])
        ->name('lecture-records.update')
        ->where('ids', '[0-9,]+');
    Route::get('/lecture-records/reports', [App\Http\Controllers\Admin\LectureRecordController::class, 'reportsIndex'])
        ->name('lecture-records.reports.index');
    Route::get('/lecture-records/reports/create', [App\Http\Controllers\Admin\LectureRecordController::class, 'reportsCreate'])
        ->name('lecture-records.reports.create');
    Route::post('/lecture-records/reports', [App\Http\Controllers\Admin\LectureRecordController::class, 'reportsStore'])
        ->name('lecture-records.reports.store');
    Route::get('/lecture-records/reports/{report}/download', [App\Http\Controllers\Admin\LectureRecordController::class, 'reportsDownload'])
        ->name('lecture-records.reports.download');
    Route::delete('/lecture-records/reports/{report}', [App\Http\Controllers\Admin\LectureRecordController::class, 'reportsDestroy'])
        ->name('lecture-records.reports.destroy');

    // Student payments
    Route::get('/payments', [App\Http\Controllers\Admin\PaymentController::class, 'index'])
        ->name('payments.index');
    Route::get('/payments/{studentId}', [App\Http\Controllers\Admin\PaymentController::class, 'show'])
        ->name('payments.show');
    Route::post('/payments/{studentId}/plan', [App\Http\Controllers\Admin\PaymentController::class, 'storePlan'])
        ->name('payments.plan.store');
    Route::post('/payments/{studentId}/payment', [App\Http\Controllers\Admin\PaymentController::class, 'storePayment'])
        ->name('payments.payment.store');
    Route::get('/payments/payment/{id}/edit', [App\Http\Controllers\Admin\PaymentController::class, 'editPayment'])
        ->name('payments.payment.edit');
    Route::put('/payments/payment/{id}', [App\Http\Controllers\Admin\PaymentController::class, 'updatePayment'])
        ->name('payments.payment.update');
    Route::delete('/payments/payment/{id}', [App\Http\Controllers\Admin\PaymentController::class, 'destroyPayment'])
        ->name('payments.payment.destroy');

    // Lecturer payments
    Route::get('/lecturer-payments', [App\Http\Controllers\Admin\LecturerPaymentController::class, 'index'])
        ->name('lecturer-payments.index');
    Route::get('/lecturer-payments/{lecturerId}', [App\Http\Controllers\Admin\LecturerPaymentController::class, 'show'])
        ->name('lecturer-payments.show');
    Route::post('/lecturer-payments/{lecturerId}', [App\Http\Controllers\Admin\LecturerPaymentController::class, 'store'])
        ->name('lecturer-payments.store');
    Route::get('/lecturer-payments/payment/{id}/edit', [App\Http\Controllers\Admin\LecturerPaymentController::class, 'edit'])
        ->name('lecturer-payments.edit');
    Route::put('/lecturer-payments/payment/{id}', [App\Http\Controllers\Admin\LecturerPaymentController::class, 'update'])
        ->name('lecturer-payments.update');
    Route::delete('/lecturer-payments/payment/{id}', [App\Http\Controllers\Admin\LecturerPaymentController::class, 'destroy'])
        ->name('lecturer-payments.destroy');
    Route::post('/students/bulk-semester-update', [App\Http\Controllers\Admin\StudentController::class, 'bulkUpdateSemester'])
        ->name('students.bulk.semester.update');

    // Certificate Verification
    Route::get('/certificates', [App\Http\Controllers\Admin\CertificateController::class, 'index'])
        ->name('certificates.index');
    Route::get('/certificates/student/{student}', [App\Http\Controllers\Admin\CertificateController::class, 'studentCertificates'])
        ->name('certificates.student');
    Route::get('/certificates/create/{student}', [App\Http\Controllers\Admin\CertificateController::class, 'create'])
        ->name('certificates.create');
    Route::post('/certificates/{student}', [App\Http\Controllers\Admin\CertificateController::class, 'store'])
        ->name('certificates.store');
    Route::get('/certificates/{certificate}/edit', [App\Http\Controllers\Admin\CertificateController::class, 'edit'])
        ->name('certificates.edit');
    Route::put('/certificates/{certificate}', [App\Http\Controllers\Admin\CertificateController::class, 'update'])
        ->name('certificates.update');
    Route::delete('/certificates/{certificate}', [App\Http\Controllers\Admin\CertificateController::class, 'destroy'])
        ->name('certificates.destroy');
});