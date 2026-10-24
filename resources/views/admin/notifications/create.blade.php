@extends('layouts.admin')
@section('title')
Send Notification
@endsection
@push('styles')
<style>
.btn-back {
        background:#fff; color:#012147; font-weight:600; padding:10px 20px;
        border-radius:10px; text-decoration:none; display:inline-flex; align-items:center; gap:8px;
        transition:0.2s;
}
.btn-back:hover { background:#e2e8f0; color:#012147;
}
.send-option {
        border:1px solid #e2e8f0; border-radius:12px; padding:12px 16px;
        cursor:pointer; display:flex; align-items:center; gap:10px; flex:1; transition:0.2s;
}
.send-option:hover { background:#f8fafc;
}
.send-option input { accent-color:#012147;
}
.send-option.active { border-color:#012147; background:#eef2f9;
}
.lecturer-list {
        border:1px solid #e2e8f0; border-radius:12px; max-height:280px; overflow-y:auto; padding:6px;
}
.lecturer-item {
        display:flex; align-items:center; gap:10px; padding:9px 12px;
        border-radius:8px; cursor:pointer; margin:0; font-size:14px;
}
.lecturer-item:hover { background:#f1f5f9;
}
.lecturer-item input { accent-color:#012147;
}
.lecturer-item small { color:#64748b;
}
.btn-send {
        background:#012147; color:#fff; border:none; font-weight:600;
        padding:12px 26px; border-radius:10px; display:inline-flex; align-items:center; gap:8px;
        transition:0.2s;
}
.btn-send:hover { background:#1e3a6e; color:#fff;
}
@media (max-width:576px) {
.send-options { flex-direction:column;
}
}
</style>
@endpush
@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h2><i class="bi bi-megaphone"></i> Send Notification</h2>
            <small>Send a message to lecturers' dashboards</small>
        </div>
        
    </div>

    

    @if($errors->any())
        <div class="alert alert-danger rounded-3">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card-box">
        <form action="{{ route('admin.notifications.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-control" value="{{ old('title') }}" maxlength="255" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Message</label>
                <textarea name="message" rows="4" class="form-control" maxlength="1000" required>{{ old('message') }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Send to</label>
                <div class="d-flex gap-3 send-options">
                    <label class="send-option {{ old('send_to', 'all') === 'all' ? 'active' : '' }}" id="optAll">
                        <input type="radio" name="send_to" value="all" {{ old('send_to', 'all') === 'all' ? 'checked' : '' }}>
                        <span><i class="bi bi-people"></i> All lecturers</span>
                    </label>
                    <label class="send-option {{ old('send_to') === 'selected' ? 'active' : '' }}" id="optSelected">
                        <input type="radio" name="send_to" value="selected" {{ old('send_to') === 'selected' ? 'checked' : '' }}>
                        <span><i class="bi bi-person-check"></i> Selected lecturers</span>
                    </label>
                </div>
            </div>

            <div class="mb-4" id="lecturerBox" style="display:none;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label class="form-label mb-0">Choose lecturers</label>
                    <button type="button" class="btn btn-link btn-sm text-decoration-none p-0" id="toggleAll">Select all</button>
                </div>
                <input type="text" id="lecturerSearch" class="form-control mb-2" placeholder="Search lecturers...">
                <div class="lecturer-list">
                    @forelse($lecturers as $lecturer)
                        <label class="lecturer-item" data-name="{{ strtolower($lecturer->name . ' ' . $lecturer->username) }}">
                            <input type="checkbox" name="lecturer_ids[]" value="{{ $lecturer->id }}"
                                {{ in_array($lecturer->id, old('lecturer_ids', [])) ? 'checked' : '' }}>
                            <span>{{ $lecturer->name }} <small>({{ $lecturer->username }})</small></span>
                        </label>
                    @empty
                        <div class="text-center text-muted p-3">No lecturers found.</div>
                    @endforelse
                </div>
            </div>

            <button type="submit" class="btn-send">
                <i class="bi bi-send"></i> Send Notification
            </button>
        </form>
    </div>
</div>
@endsection
@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const radios = document.querySelectorAll('input[name="send_to"]');
    const box = document.getElementById('lecturerBox');
    const optAll = document.getElementById('optAll');
    const optSelected = document.getElementById('optSelected');
    const checks = document.querySelectorAll('input[name="lecturer_ids[]"]');
    const toggleBtn = document.getElementById('toggleAll');
    const search = document.getElementById('lecturerSearch');

    function updateMode() {
        const mode = document.querySelector('input[name="send_to"]:checked').value;
        box.style.display = mode === 'selected' ? 'block' : 'none';
        optAll.classList.toggle('active', mode === 'all');
        optSelected.classList.toggle('active', mode === 'selected');
    }

    radios.forEach(r => r.addEventListener('change', updateMode));
    updateMode();

    toggleBtn.addEventListener('click', function () {
        const visible = [...checks].filter(c => c.closest('.lecturer-item').style.display !== 'none');
        const allChecked = visible.every(c => c.checked);
        visible.forEach(c => c.checked = !allChecked);
        this.innerText = allChecked ? 'Select all' : 'Clear all';
    });

    search.addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('.lecturer-item').forEach(item => {
            item.style.display = item.dataset.name.includes(q) ? 'flex' : 'none';
        });
    });
});
</script>
@endpush
