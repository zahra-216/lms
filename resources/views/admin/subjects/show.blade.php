@extends('layouts.admin')
@section('title')
{{ $subject->name }} - Module
@endsection
@push('styles')
<style>
.module-card { border-radius:15px; text-decoration:none; color:#012147; background:#fff; padding:35px 20px; text-align:center; display:block; transition:0.2s; box-shadow:0 6px 20px rgba(0,0,0,0.06);
}
.module-card:hover { transform:translateY(-5px); color:#012147; box-shadow:0 10px 26px rgba(0,0,0,0.1);
}
.module-card i { font-size:1.8rem; margin-bottom:12px; display:flex; align-items:center; justify-content:center; width:56px; height:56px; border-radius:14px; color:#fff; margin:0 auto 12px;
}
.icon-notes { background:#3b82f6;
}
.icon-assignments { background:#f59e0b;
}
.icon-quizzes { background:#ec4899;
}
.icon-grades { background:#10b981;
}
.icon-attendance { background:#ef4444;
}
.icon-timetable { background:#8b5cf6;
}
.icon-recordings { background:#06b6d4;
}
</style>
@endpush
@section('content')
<div class="container">
    

    <div class="page-header">
        <h2><i class="bi bi-mortarboard"></i> {{ $subject->code }} - {{ $subject->name }}</h2>
        <small>Choose a module below</small>
    </div>

    <div class="row g-4">
        <div class="col-md-4 col-6">
            <a href="{{ route('admin.subjects.notes.index', $subject->id) }}" class="module-card">
                <i class="bi bi-file-earmark-text icon-notes"></i><span>Notes</span>
            </a>
        </div>
        <div class="col-md-4 col-6">
            <a href="{{ route('admin.subjects.assignments.index', $subject->id) }}" class="module-card">
                <i class="bi bi-journal-text icon-assignments"></i><span>Assignments</span>
            </a>
        </div>
        <div class="col-md-4 col-6">
            <a href="{{ route('admin.subjects.quizzes.index', ['subject' => $subject->id]) }}" class="module-card">
                <i class="bi bi-puzzle icon-quizzes"></i><span>Quizzes</span>
            </a>
        </div>
        <div class="col-md-4 col-6">
            <a href="{{ route('admin.subjects.grades', $subject->id) }}" class="module-card">
                <i class="bi bi-clipboard-data icon-grades"></i><span>Grades</span>
            </a>
        </div>
        <div class="col-md-4 col-6">
            <a href="{{ route('admin.subjects.recordings.index', $subject->id) }}" class="module-card">
                <i class="bi bi-collection-play icon-recordings"></i><span>Recordings</span>
            </a>
        </div>
        <div class="col-md-4 col-6">
            <a href="{{ route('admin.attendance.show', $subject->id) }}" class="module-card">
                <i class="bi bi-calendar-check icon-attendance"></i><span>Attendance</span>
            </a>
        </div>
        <div class="col-md-4 col-6">
            <a href="{{ route('admin.subjects.timetables.index', $subject->id) }}" class="module-card">
                <i class="bi bi-calendar-week icon-timetable"></i><span>Timetable</span>
            </a>
        </div>
    </div>
</div>
@endsection
