<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Notes - {{ $subject->name }}</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
body{
    background:#f4f6fb;
    font-family:'Segoe UI', sans-serif;
}

/* TOP BAR */
.topbar{
    background: linear-gradient(135deg, #012147, #1a3d7c);
    color:#fff;
    padding:18px 25px;
    border-radius:14px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 10px 25px rgba(0,0,0,0.1);
}

.topbar h3{
    margin:0;
    font-size:20px;
}

/* ADD BUTTON */
.btn-add{
    background:#fff;
    color:#012147;
    font-weight:600;
    padding:8px 14px;
    border-radius:10px;
    text-decoration:none;
    transition:0.3s;
}

.btn-add:hover{
    transform:scale(1.05);
}

/* CARD TABLE */
.card-box{
    background:#fff;
    border-radius:14px;
    padding:18px;
    margin-top:20px;
    box-shadow:0 8px 25px rgba(0,0,0,0.06);
}

/* TABLE */
.table thead{
    background:#012147;
    color:#fff;
}

.table tbody tr{
    transition:0.2s;
}

.table tbody tr:hover{
    background:#eef3ff;
}

/* BADGES */
.badge-yes{
    background:#198754;
    padding:5px 10px;
    border-radius:20px;
    color:#fff;
}

.badge-no{
    background:#6c757d;
    padding:5px 10px;
    border-radius:20px;
    color:#fff;
}

/* ACTION BUTTONS */
.btn-icon{
    border:none;
    padding:6px 10px;
    border-radius:8px;
    color:#fff;
    font-size:13px;
}

.edit{
    background:#ffc107;
    color:#000;
}

.delete{
    background:#dc3545;
}

.download{
    background:#198754;
}

.open{
    background:#0d6efd;
}

/* RESPONSIVE */
@media(max-width:768px){
    .topbar{
        flex-direction:column;
        gap:10px;
        text-align:center;
    }
}
</style>
</head>

<body>

<div class="container mt-4">

    <!-- TOP HEADER -->
    <div class="topbar">
        <h3><i class="fa fa-book me-2"></i> {{ $subject->name }} - Notes</h3>

        <a href="{{ route('admin.subjects.notes.create', $subject->id) }}" class="btn-add">
            <i class="fa fa-plus me-1"></i> Add Note
        </a>
    </div>

    <!-- SUCCESS -->
    @if(session('success'))
        <div class="alert alert-success mt-3">
            {{ session('success') }}
        </div>
    @endif

    <!-- TABLE CARD -->
    <div class="card-box">

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Type</th>
                        <th>File / URL</th>
                        <th>Order</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                @foreach($notes as $note)
                    <tr>
                        <td><b>{{ $note->title }}</b></td>
                        <td>{{ ucfirst($note->type) }}</td>

                        <td>
                            @if($note->url)
                                <a href="{{ $note->url }}" target="_blank" class="btn-icon open">
                                    <i class="fa fa-link"></i>
                                </a>
                            @endif

                            @if($note->file_path)
                                <a href="{{ route('admin.subjects.notes.download', [$subject->id, $note->id]) }}" class="btn-icon download">
                                    <i class="fa fa-download"></i>
                                </a>
                            @endif
                        </td>

                        <td>{{ $note->order }}</td>

                        <td>
                            @if($note->is_published)
                                <span class="badge-yes">Published</span>
                            @else
                                <span class="badge-no">Hidden</span>
                            @endif
                        </td>

                        <td class="d-flex gap-1">

                            <a href="{{ route('admin.subjects.notes.edit', [$subject->id,$note->id]) }}" class="btn-icon edit">
                                <i class="fa fa-edit"></i>
                            </a>

                            <form action="{{ route('admin.subjects.notes.destroy', [$subject->id,$note->id]) }}" method="POST">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn-icon delete" onclick="return confirm('Delete this note?')">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>

                        </td>
                    </tr>
                @endforeach
                </tbody>

            </table>
        </div>

    </div>

    <!-- PAGINATION -->
    <div class="mt-3">
        {{ $notes->links() }}
    </div>

</div>

</body>
</html>