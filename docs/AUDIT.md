# LMS audit, fixes and recommendations

Branch: `redesign-and-fixes`. This page records what was found in the review, what was changed, and what is still recommended.

## 1. How the quiz works

| Step | What happens |
|---|---|
| Lecturer / admin creates a quiz | Title, duration (minutes), total points, max attempts, grading type (`automatic`, `manual`, `both`), start and end date, "show correct answers" switch. Questions are added on the edit page. A quiz **cannot be edited or deleted once its start date has passed**. |
| Question types | `multiple_choice` (one correct option), `true_false`, `short_answer` (written). |
| Student starts | Allowed while the quiz is published, started and not ended, and attempts used < max attempts. The attempt records `started_at`; the deadline is `started_at + duration`. |
| Student answers | Choice questions: pick an option. Written questions: a large text box. **Long answers are accepted**: the database column is `TEXT` (about 65,000 characters) and validation allows up to 50,000 characters. |
| Submit / time out | The page counts down and submits by itself at 0:00. Blank answers are allowed. The **server enforces the limit too**: answers arriving more than 60 seconds after the deadline are not accepted. |
| Marking | Choice questions are marked automatically (points added to `automatic_score`). **Written answers always wait for the lecturer**, even on an "automatic" quiz, who marks them on the grade page. |
| Result | The student sees the score; correct answers are shown only after grading and only if "show correct answers" is on. |

### Quiz bugs fixed
1. Time ran out with any question blank -> validation failed, **every answer was lost**, student got 0 and an error page. Blank answers are now allowed.
2. Reloading an attempt after time had run out sent the browser to a POST-only address (405 error).
3. The remaining-time sum was wrong under Carbon 3 (it counted time *up*), so a reload could give **more** time than allowed and an expired attempt never ended.
4. Time limit was only enforced by JavaScript. It is now enforced on the server.
5. "Automatic" quizzes marked the attempt as fully graded even when it had written answers. Now they wait for the lecturer.
6. A choice question with no correct option crashed on submit; an answer id belonging to a different question was accepted. Both handled.
7. Quiz page: answered counter, warning about unanswered questions, auto-saved draft in the browser, character counter.

## 2. Security fixes

| Severity | Problem | Fix |
|---|---|---|
| **Critical** | Students could upload **any file type, any size** as assignment work; lecturers any file as notes; files are web-reachable. A `.php` upload could be run on the server. | Allow-lists of types (documents, slides, spreadsheets, archives, images) and size limits; content is checked, not just the file name. `.htaccess` in `storage/app/public` and `public/uploads` blocks script execution. |
| High | No limit on student / lecturer password guesses. | 5 tries then a 2 minute wait (per account and address), same as the admin login. |
| High | Student login said whether a registration number existed. | One generic message. |
| Medium | Session id not renewed after student login (session fixation). | `session()->regenerate()` after sign-in. |
| Medium | A student could open any subject's notes, videos, quizzes and grades by changing the number in the address. | Student pages are limited to subjects of the student's own course and level. |
| Medium | A note, video or quiz could be changed through a different subject's address. | Content under `/subject/{id}` must belong to that subject (otherwise 404). |
| Low | Note `type` accepted any text; the database accepts five values, so a wrong value crashed. | Validated against the allowed list. |

## 3. Bugs and clean-up

* 14 pages returned a server error: unused `show` / `create` addresses on students, lecturers, faculties, courses, levels, semesters, subjects; the admin assignments list; three old student pages; a student quiz address; a public `/course/{id}/levels` address. Routes that nothing used were removed or redirected.
* About 20 unused view files and 3 unused controller methods deleted.
* Faculty cards and the lecturer course browser fetched random pictures from `picsum.photos` / `via.placeholder.com`; they now use the campus photo.
* `user.png` and `admin_avatar.png` are referenced but do not exist; initials avatars are used instead.
* After saving or deleting, people are now sent **back to where they came from** (filters and page number kept) instead of a fixed list. See `Controller::returnTo()` and `RememberPreviousPage`.
* One **Back** button on every inner page (it was on 87 of 143, and some pages had two).

## 4. How the interface is built now

* `layouts/admin`, `layouts/lecturer`, `layouts/student` (sidebar, top bar, notifications, Back button, messages), `layouts/guest` (sign-in), `layouts/public` (home, certificate check, grade scale).
* One stylesheet: `public/css/lms.css` (colours and fonts match the website). One script: `public/js/lms.js`.
* A normal page is now only its own content:

```blade
@extends('layouts.admin')
@section('title') Students @endsection
@section('back', route('admin.dashboard'))   {{-- optional parent page --}}
@section('content') ... @endsection
```
* Use `@section('no_back', '1')` on a dashboard to hide the Back button.
* PDF views are separate full documents and were not changed.

## 5. Still recommended (not changed, needs a decision)

1. **Assign lecturers to subjects.** The database has no lecturer-subject link, so every lecturer can open every subject. Add a `lecturer_subject` table (or use the timetable) and tighten `EnsureLecturerOwnsSubject`.
2. **Enforce subject unlock on the server.** The "enter subject code" step only guards the dashboard flow; the pages themselves are open to any student of that course.
3. **Password reset for students and lecturers** (only admins have it).
4. **Set `APP_DEBUG=false`** in the live `.env` and make sure `APP_ENV=production`.
5. Use `$fillable` / validation on every model write (many models still use broad mass assignment).
6. Move queries out of Blade views (the old dashboards counted rows inside the template).
7. Add automated tests (the `tests/` folder is empty). Quiz timing and upload rules are the first candidates.
8. Back up the database and `storage/app/public` on a schedule.

## 6. What a full LMS usually has that this one does not (yet)

Announcements per course; discussion forum; assignment feedback and re-submission; question bank with shuffling and random selection; server-side quiz autosave and review of answers; progress / completion tracking per student; student view of own attendance and timetable; export of grades and attendance to Excel; bulk import of students (CSV); e-mail notifications; admin roles and permissions; audit log; two-factor sign-in for admins; accessibility review.

## 7. How it was tested

* A crawler signs in as admin, lecturer and student on a copy of the database and opens every page: **137 pages, 0 errors** (the remaining 26 "not found" results are sample records that do not exist in the test data).
* Quiz scenarios (blank answers on time-out, 30,000-character written answer, late submission, expired attempt, wrong answer id): 10 / 10.
* Upload rules and delete-returns-to-list: 5 / 5. Edit-returns-to-list: 2 / 2.
