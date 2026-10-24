# TT Metro Campus LMS

Learning management system for TT Metro Campus (Laravel 12, PHP 8.2+, MySQL, Bootstrap 5).

## Who uses it

| Role | Sign in at | What they do |
|---|---|---|
| Student | `/login` (registration number) | Subjects, notes, videos, assignments, quizzes, grades, payments, chat |
| Lecturer | `/lecturer/login` | Notes, videos, recordings, assignments, quizzes, marking, lecture records, chat |
| Admin | `/admin/login` | Students, lecturers, faculties, courses, levels, semesters, subjects, attendance (QR scan), gate log, payments, certificates, notifications |
| Public | `/`, `/verify`, `/student-grade` | Landing page, certificate verification, grade scale |

## Run it locally

```bash
composer install
cp .env.example .env        # set DB_DATABASE / DB_USERNAME / DB_PASSWORD
php artisan key:generate
php artisan migrate
php artisan serve
```

## Update the live site

```bash
git pull
php artisan optimize:clear
```

Run `composer install --no-dev --optimize-autoloader` and `php artisan migrate --force` only when the update includes new packages or migrations.
Keep `APP_DEBUG=false` and `APP_ENV=production` in the live `.env`.

## Code layout

* `resources/views/layouts` - `admin`, `lecturer`, `student`, `guest`, `public` layouts; `partials/shell` holds the sidebar, top bar and messages.
* `public/css/lms.css`, `public/js/lms.js` - the only stylesheet and script shared by all pages.
* `app/Http/Controllers/Controller.php` - `returnTo()` sends users back to the page they came from after saving or deleting.
* `docs/AUDIT.md` - review notes, how the quiz works, what was fixed, what is still recommended.
