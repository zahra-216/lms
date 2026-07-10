<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;


/*
|--------------------------------------------------------------------------
| CONTROLLERS
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\FrontendController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LecturerAuthController;
use App\Http\Controllers\Student\StudentController as StudentStudentController;
use App\Http\Controllers\Student\SubjectController as StudentSubjectController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;


use App\Http\Controllers\Admin\{
    AuthController,
    ProfileController,
    NotificationController,
    StudentController,    LecturerController,    FacultyController,
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





Route::prefix('student')->group(function () {

    Route::get('/grades', [StudentStudentController::class, 'grades'])
        ->name('student.grades');

    Route::get('/subject/{id}/grades', [StudentStudentController::class, 'subjectGrades'])
        ->name('student.subject.grades');
});

/*
|--------------------------------------------------------------------------
| FRONTEND
|--------------------------------------------------------------------------
*/

// HOME
Route::get('/', [FrontendController::class, 'home'])->name('home');

// FACULTY → COURSES (🔥 FIXED NAME)
Route::get('/faculty/{facultyId}/courses', [FrontendController::class, 'facultyCourses'])
    ->name('faculty.courses');

// COURSE → LEVELS
Route::get('/course/{courseId}/levels', [FrontendController::class, 'courseLevels']);

// LOGIN
Route::get('/login', [FrontendController::class, 'loginPage'])->name('login');
Route::post('/login', [FrontendController::class, 'login'])->name('login.submit');

// LECTURER LOGIN
Route::get('/lecturer/login', [LecturerAuthController::class, 'showLoginForm'])->name('lecturer.login');
Route::post('/lecturer/login', [LecturerAuthController::class, 'login'])->name('lecturer.login.submit');
Route::get('/lecturer/dashboard', [LecturerAuthController::class, 'dashboard'])
    ->middleware('auth:lecturer')
    ->name('lecturer.dashboard');
Route::post('/lecturer/logout', [LecturerAuthController::class, 'logout'])->name('lecturer.logout');

Route::prefix('lecturer')->middleware(['auth:lecturer'])->name('lecturer.')->group(function () {

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

    Route::get('/assignment/{assignment}/marks/create', [App\Http\Controllers\Admin\MarkController::class, 'lecturerCreate'])
        ->name('marks.create');

    Route::post('/marks/store', [App\Http\Controllers\Admin\MarkController::class, 'lecturerStore'])
        ->name('marks.store');

    // ================= NOTES =================
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

    // ================= VIDEOS =================
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

    // ================= ASSIGNMENT CREATION =================
    Route::get('/subject/{subject}/assignments/create', [App\Http\Controllers\LecturerAssignmentController::class, 'create'])
        ->name('assignments.create');
    Route::post('/subject/{subject}/assignments', [App\Http\Controllers\LecturerAssignmentController::class, 'store'])
        ->name('assignments.store');

});

// ADMIN LOGIN (🔥 FIXED)
Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.submit');

// DASHBOARD
Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

// LOGOUT
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

Route::get('/student/subject/{id}/assignments',
    [StudentSubjectController::class, 'assignments']
)->name('student.subject.assignments');


Route::post('/assignment/submit', [AssignmentController::class, 'submit'])
    ->name('assignment.submit');

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/
/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->middleware(['auth:admin'])->name('admin.')->group(function () {

    // ================= DASHBOARD =================
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])
        ->name('dashboard');

    // ================= LOGOUT =================
    Route::post('/logout', function () {
        Auth::guard('admin')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('admin.login');
    })->name('logout');

    // ================= CRUD =================
    Route::resources([
        'students' => StudentController::class,
        'lecturers' => LecturerController::class,
        'faculties' => FacultyController::class,
        'courses' => CourseController::class,
        'levels' => LevelController::class,
        'semesters' => SemesterController::class,
        'subjects' => SubjectController::class,
        'enrollments' => EnrollmentController::class,
        'assignments' => AssignmentController::class,
    ]);

    // ================= AJAX =================
    Route::get('/get-levels/{courseId}', [LevelController::class, 'getByCourse'])
        ->name('levels.byCourse');

    Route::get('/get-semesters/{levelId}', [SemesterController::class, 'getByLevel'])
        ->name('semesters.byLevel');

    Route::get('/semester/{id}/subjects', [SemesterController::class, 'getSubjects'])
        ->name('semester.subjects');

    // ================= NOTES =================
    Route::prefix('subjects/{subject}')->name('subjects.')->group(function () {

        Route::get('notes', [NoteController::class, 'index'])->name('notes.index');
        Route::get('notes/create', [NoteController::class, 'create'])->name('notes.create');
        Route::post('notes', [NoteController::class, 'store'])->name('notes.store');
        Route::get('notes/{note}/edit', [NoteController::class, 'edit'])->name('notes.edit');
        Route::put('notes/{note}', [NoteController::class, 'update'])->name('notes.update');
        Route::delete('notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');
        Route::get('notes/{note}/download', [NoteController::class, 'download'])->name('notes.download');
        
    });

});
/*
|--------------------------------------------------------------------------
| ONLINE USERS
|--------------------------------------------------------------------------
*/

Route::get('/online-users', function () {

    return Student::whereNotNull('last_seen_at')
        ->where('last_seen_at', '>=', now()->subMinutes(5))
        ->get(['id', 'name', 'registration_no']);
});

/*
|--------------------------------------------------------------------------
| STUDENT FLOW
|--------------------------------------------------------------------------
*/

// VERIFY SUBJECT
Route::post('/student/verify-subject', [StudentSubjectController::class, 'verifySubject']);

// SEMESTER → SUBJECTS
Route::get('/student/semester/{id}/subjects', [SemesterController::class, 'getSubjects']);

// SUBJECT NOTES PAGE
Route::get('/student/subject/{id}/notes', [StudentSubjectController::class, 'notes']);

// STUDENT DOWNLOAD (FIXED)
Route::get('/student/download/{id}', [StudentSubjectController::class, 'download'])
    ->name('student.note.download');




/* ================= STUDENT PROFILE ================= */
Route::get('/student/profile', function () {

    $student = \App\Models\Student::find(session('student_id'));

    if (!$student) {
        return redirect()->route('login');
    }

    return view('student.profile', compact('student'));

})->name('student.profile');


/* ================= PHOTO UPLOAD ================= */
Route::post('/student/photo-update', [App\Http\Controllers\Admin\StudentController::class, 'updatePhoto'])
->name('student.photo.update');
    Route::get('/student/my-courses', [DashboardController::class, 'myCourses'])
    ->name('student.my.courses');





Route::prefix('admin')->group(function () {

    // Assignments
    Route::get('/assignments', [AssignmentController::class,'index'])
        ->name('admin.assignments.index');

    Route::get('/assignments/create', [AssignmentController::class,'create'])
        ->name('admin.assignments.create');

    Route::post('/assignments/store', [AssignmentController::class,'store'])
        ->name('admin.assignments.store');

    // AJAX
    Route::get('/get-subjects', [AjaxController::class,'getSubjects']);
    Route::get('/get-notes/{subject_id}', [AjaxController::class,'getNotes']);

});
Route::get('/student/assignment/download/{id}', [SubjectController::class, 'downloadAssignment'])
    ->name('student.assignment.download');
    Route::get('/admin/assignments/{id}/submissions',
    [AssignmentController::class, 'submissions']
)->name('admin.assignments.submissions');
Route::prefix('admin')->group(function () {

    // 📄 PDF (MUST BE FIRST - VERY IMPORTANT)
    Route::get('enrollments/export/pdf', [EnrollmentController::class, 'exportPdf'])
        ->name('admin.enrollments.pdf');

    // 📋 LIST
    Route::get('enrollments', [EnrollmentController::class, 'index'])
        ->name('admin.enrollments.index');

    // ➕ CREATE
    Route::get('enrollments/create', [EnrollmentController::class, 'create'])
        ->name('admin.enrollments.create');

    // 💾 STORE
    Route::post('enrollments', [EnrollmentController::class, 'store'])
        ->name('admin.enrollments.store');

    // ✏ EDIT
    Route::get('enrollments/{id}/edit', [EnrollmentController::class, 'edit'])
        ->name('admin.enrollments.edit');

    // 🔄 UPDATE
    Route::put('enrollments/{id}', [EnrollmentController::class, 'update'])
        ->name('admin.enrollments.update');

    // 🗑 DELETE
    Route::delete('enrollments/{id}', [EnrollmentController::class, 'destroy'])
        ->name('admin.enrollments.delete');
});




/*
|--------------------------------------------------------------------------
| ADMIN AUTH (PUBLIC ROUTES - NO MIDDLEWARE)
|--------------------------------------------------------------------------
*/

// CREATE ADMIN
Route::get('/admin/create', [AdminController::class, 'create'])->name('admin.create');
Route::post('/admin/create', [AdminController::class, 'store'])->name('admin.store');

/*
|--------------------------------------------------------------------------
| ADMIN FORGOT PASSWORD (PUBLIC - IMPORTANT)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->group(function () {

    // FORGOT PASSWORD FORM
    Route::get('/forgot-password', [AdminController::class, 'forgotForm'])
        ->name('admin.password.request');

    // SEND RESET LINK
    Route::post('/forgot-password', [AdminController::class, 'sendResetLink'])
        ->name('admin.password.email');

    // RESET PASSWORD FORM (CLICK LINK)
    Route::get('/reset-password/{token}', [AdminController::class, 'resetForm'])
        ->name('admin.password.reset');

    // RESET PASSWORD SUBMIT
    Route::post('/reset-password', [AdminController::class, 'resetPassword'])
        ->name('admin.password.update');
});
Route::prefix('admin')->middleware(['auth:admin'])->group(function () {

    Route::get('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'show'])
        ->name('admin.profile');

    Route::post('/profile/update', [App\Http\Controllers\Admin\ProfileController::class, 'update'])
        ->name('admin.profile.update');
});
Route::prefix('admin')->middleware(['auth:admin'])->group(function () {

    Route::get('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'index'])
        ->name('admin.settings');

    Route::post('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'update'])
        ->name('admin.settings.update');
});
Route::middleware(['auth:admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::post('/notification/read/{id}', function ($id) {
        auth('admin')->user()->notifications()->where('id', $id)->update(['read_at' => now()]);
        return response()->json(['success' => true]);
    })->name('notification.read');

    Route::post('/notification/read-all', function () {
        auth('admin')->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    })->name('notification.readAll');

});


// ADMIN
Route::get('/admin/marks/{assignment_id}', [MarkController::class, 'create'])
    ->name('admin.marks.create');

Route::post('/admin/marks/store', [MarkController::class, 'store'])
    ->name('admin.marks.store');

Route::get('/admin/marks', [MarkController::class, 'index'])
    ->name('admin.marks.index');
Route::get('/student-grade', function () {
    return view('student-grade');
});