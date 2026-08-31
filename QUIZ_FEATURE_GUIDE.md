# Quiz/MCQ Feature - Complete Implementation Guide

## Overview
A comprehensive quiz system has been implemented for your LMS with the following features:

### Features Implemented ✅
- **Question Types**: Multiple Choice, True/False, Short Answer
- **Grading Options**: Automatic (for MC/T-F), Manual, or Both
- **Quiz Scheduling**: Start/End dates with automatic lock after start
- **Attempt Management**: Configurable max attempts per student
- **Time Limits**: Countdown timer during quiz taking
- **Analytics**: View attempts, unique students, average scores
- **Instant Feedback**: Auto-grading for MC/T-F questions
- **Manual Grading**: Grade short answers and provide remarks
- **Role-Based Access**: Lecturers, Admins, and Students

---

## Database Tables Created

### 1. `quizzes`
Stores quiz configuration
- `id`, `subject_id`, `title`, `description`
- `start_date`, `end_date`, `duration_minutes`
- `total_points`, `max_attempts`
- `grading_type` (automatic/manual/both)
- `show_correct_answers`, `is_published`

### 2. `quiz_questions`
Individual questions in a quiz
- `id`, `quiz_id`, `type` (multiple_choice/true_false/short_answer)
- `question_text`, `points`, `order`, `correct_answer`

### 3. `quiz_answers`
Answer options for questions
- `id`, `quiz_question_id`, `answer_text`, `is_correct`, `order`

### 4. `quiz_submissions`
Student quiz attempts
- `id`, `quiz_id`, `student_id`
- `started_at`, `submitted_at`, `attempt_number`
- `automatic_score`, `manual_score`, `lecturer_remarks`
- `status` (in_progress/submitted/graded)

### 5. `quiz_submission_answers`
Individual student answers
- `id`, `quiz_submission_id`, `quiz_question_id`
- `quiz_answer_id`, `answer_text`, `is_correct`

---

## Files Created

### Database Migrations
```
database/migrations/2026_08_31_000001_create_quizzes_table.php
database/migrations/2026_08_31_000002_create_quiz_questions_table.php
database/migrations/2026_08_31_000003_create_quiz_answers_table.php
database/migrations/2026_08_31_000004_create_quiz_submissions_table.php
database/migrations/2026_08_31_000005_create_quiz_submission_answers_table.php
```

### Models
```
app/Models/Quiz.php
app/Models/QuizQuestion.php
app/Models/QuizAnswer.php
app/Models/QuizSubmission.php
app/Models/QuizSubmissionAnswer.php
```

### Controllers
```
app/Http/Controllers/LecturerQuizController.php        (Lecturer operations)
app/Http/Controllers/Admin/QuizController.php          (Admin operations)
app/Http/Controllers/Student/QuizController.php        (Student quiz taking)
```

### Lecturer Views
```
resources/views/lecturer/quizzes/index.blade.php       (List quizzes)
resources/views/lecturer/quizzes/create.blade.php      (Create quiz)
resources/views/lecturer/quizzes/edit.blade.php        (Edit quiz & manage questions)
resources/views/lecturer/quizzes/show.blade.php        (View details & submissions)
resources/views/lecturer/quizzes/grade.blade.php       (Grade submission)
```

### Admin Views
```
resources/views/admin/quizzes/index.blade.php
resources/views/admin/quizzes/create.blade.php
resources/views/admin/quizzes/edit.blade.php
resources/views/admin/quizzes/show.blade.php
resources/views/admin/quizzes/grade.blade.php
```

### Student Views
```
resources/views/student/quizzes/index.blade.php        (Available quizzes)
resources/views/student/quizzes/attempt.blade.php      (Quiz with timer)
resources/views/student/quizzes/result.blade.php       (View result)
```

---

## Routes Added

### Lecturer Routes (prefix: `/lecturer`)
```
GET    /subject/{subject}/quizzes                      → index
GET    /subject/{subject}/quizzes/create               → create
POST   /subject/{subject}/quizzes                      → store
GET    /subject/{subject}/quizzes/{quiz}               → show
GET    /subject/{subject}/quizzes/{quiz}/edit          → edit
PUT    /subject/{subject}/quizzes/{quiz}               → update
DELETE /subject/{subject}/quizzes/{quiz}               → destroy

POST   /subject/{subject}/quizzes/{quiz}/questions     → storeQuestion
PUT    /subject/{subject}/quizzes/{quiz}/questions/{question}  → updateQuestion
DELETE /subject/{subject}/quizzes/{quiz}/questions/{question}  → destroyQuestion

GET    /subject/{subject}/quizzes/{quiz}/submissions/{submission}/grade  → gradeSubmission
POST   /subject/{subject}/quizzes/{quiz}/submissions/{submission}/grades → saveGrades
```

### Admin Routes (prefix: `/admin/subjects/{subject}`)
```
GET    quizzes                                          → index
GET    quizzes/create                                   → create
POST   quizzes                                          → store
GET    quizzes/{quiz}                                   → show
GET    quizzes/{quiz}/edit                              → edit
PUT    quizzes/{quiz}                                   → update
DELETE quizzes/{quiz}                                   → destroy

POST   quizzes/{quiz}/questions                         → storeQuestion
PUT    quizzes/{quiz}/questions/{question}              → updateQuestion
DELETE quizzes/{quiz}/questions/{question}              → destroyQuestion

GET    quizzes/{quiz}/submissions/{submission}/grade    → gradeSubmission
POST   quizzes/{quiz}/submissions/{submission}/grades   → saveGrades
```

### Student Routes (prefix: `/student`)
```
GET    /subject/{id}/quizzes                            → index (quiz list)
POST   /quiz/{quiz}/start                               → start (create submission)
GET    /quiz/{submission}/attempt                       → attempt (take quiz)
POST   /quiz/{submission}/submit                        → submit (finish quiz)
GET    /quiz/{submission}/result                        → result (view result)
```

---

## Installation & Setup

### Step 1: Run Migrations
```bash
php artisan migrate
```

### Step 2: Test the Feature

#### Lecturer/Admin Side:
1. Navigate to: `/lecturer/subject/{id}` or Admin dashboard
2. You should see a "Quizzes" link/section (add this to your subject view if needed)
3. Click "Create Quiz"
4. Fill quiz details:
   - Title, Description
   - Duration, Total Points
   - Max Attempts
   - Grading Type (Automatic/Manual/Both)
   - Start/End Dates
   - Publish toggle

5. Add Questions:
   - Multiple Choice: Set options and mark correct answer
   - True/False: Select correct answer
   - Short Answer: Provide model answer
   - Each question has points

6. Publish the quiz
7. View student submissions and grade them

#### Student Side:
1. Navigate to: `/student/subject/{id}/quizzes`
2. See available quizzes
3. Click "Start Quiz"
4. Answer questions (with countdown timer)
5. Submit quiz
6. View results (after grading)

---

## Key Features & Usage

### Creating a Quiz
```
1. Set title, description
2. Configure duration (in minutes)
3. Set total points
4. Set max attempts allowed
5. Choose grading type
6. Set start/end dates (after start, can't edit/delete)
7. Publish to make visible to students
```

### Question Types

**Multiple Choice:**
- Add 2+ options
- Mark one as correct
- Student selects one
- Auto-graded

**True/False:**
- Two options provided
- Mark correct answer
- Student selects one
- Auto-graded

**Short Answer:**
- Student types response
- Manual grading by lecturer
- Provide model answer as reference

### Grading

**Automatic:**
- Only MC/T-F questions
- Score calculated immediately
- Student can see score right after submission

**Manual:**
- All question types
- Lecturer reviews and grades
- Can add remarks/feedback
- Student waits for grading

**Both:**
- Auto grades MC/T-F
- Lecturer grades short answers + manually adjusts if needed
- Final score is average or sum (configurable)

### Time Limits
- Countdown timer displays during quiz
- Auto-submits when time expires
- Students cannot exceed time limit

### Attempt Limits
- Set max attempts per student
- Can be 1 (single attempt) or unlimited
- Shows current attempt number
- Prevents exceeding max

### Cannot Edit After Start
- Once start_date passes, quiz is locked
- Lecturers cannot edit or delete
- Ensures fairness for students

---

## Model Methods & Helpers

### Quiz Model
```php
$quiz->hasStarted()          // Check if start date passed
$quiz->hasEnded()            // Check if end date passed
$quiz->isAvailable()         // Check if can be taken now
$quiz->canBeEdited()         // Check if can be modified
```

### QuizSubmission Model
```php
$submission->getTotalScore()  // Get final score
$submission->isGraded()       // Check if fully graded
```

---

## Important Notes

1. **Quiz Locking**: Once `start_date` passes, the quiz cannot be edited or deleted. Plan dates carefully!

2. **Auto-Grading**: Only works for MC and T-F questions. Short answers need manual review.

3. **Student View**: Students only see their own submissions and results.

4. **Admin Access**: Admins have full control like lecturers.

5. **Feedback Timing**: Students don't see grading details until lecturer completes grading.

6. **Correct Answers**: Can optionally show correct answers after grading via `show_correct_answers` toggle.

---

## Troubleshooting

### Quiz Not Showing for Students
- Check `is_published = 1`
- Check `start_date <= now()` and `end_date >= now()`
- Check student is enrolled in the subject

### Questions Not Showing
- Ensure they're saved (check database)
- Check question `order` field

### Grading Not Working
- Verify `grading_type` is set
- For manual grading, fill `manual_score`
- For short answers, manually mark `is_correct`

### Timer Issues
- Timer is in JavaScript, refreshes on page load
- Auto-submits at 0 seconds
- Adjust `duration_minutes` in quiz settings

---

## Future Enhancements (Optional)

1. Randomize question order
2. Randomize answer options
3. Question pools (random selection)
4. Partial credit for MC
5. Analytics dashboard (per-question performance)
6. Quiz reviews/retakes
7. Export results to PDF
8. Weighted scoring
9. Negative marking (wrong answer = negative points)
10. Anonymous grading

---

## API Usage Examples

### Create a Quiz (Lecturer)
```php
// In controller
$quiz = Quiz::create([
    'subject_id' => $subject->id,
    'title' => 'Midterm Exam',
    'description' => 'Test your knowledge',
    'duration_minutes' => 60,
    'total_points' => 100,
    'max_attempts' => 1,
    'grading_type' => 'automatic',
    'is_published' => true,
    'start_date' => now(),
    'end_date' => now()->addDays(7),
]);
```

### Get Student's Best Score
```php
$bestScore = $student->quizSubmissions()
    ->where('quiz_id', $quiz->id)
    ->whereNotNull('manual_score')
    ->max('manual_score');
```

### Get Quiz Statistics
```php
$stats = $quiz->submissions()
    ->whereNotNull('manual_score')
    ->select(
        DB::raw('COUNT(*) as total_submissions'),
        DB::raw('AVG(manual_score) as avg_score'),
        DB::raw('MAX(manual_score) as max_score'),
        DB::raw('MIN(manual_score) as min_score')
    )
    ->first();
```

---

## Support

For any issues or questions, refer to:
1. Model files for relationships
2. Controller files for business logic
3. View files for UI structure
4. Database migrations for schema

All files are documented with comments for reference.

---

**Quiz Feature Implementation Complete! 🎉**
