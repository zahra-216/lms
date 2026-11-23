@extends('layouts.admin')
@section('title')
YouTube Playlists
@endsection
@section('back', route('admin.youtube.show'))
@push('styles')
<style>
.yp-table td, .yp-table th { white-space: normal; }
.yp-table select { min-width: 220px; }
.yp-note { font-size: 12.5px; color: #64748b; margin-top: 4px; }
</style>
@endpush
@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h2><i class="bi bi-collection-play"></i> YouTube Playlists</h2>
            <small>Each programme's class recordings go into one playlist on the campus channel</small>
        </div>
    </div>

    @if(! $canManage)
        <div class="alert alert-warning rounded-3"><i class="bi bi-exclamation-triangle"></i> <div>YouTube was connected before playlists were added, so it is missing one permission. On the <a href="{{ route('admin.youtube.show') }}" class="alert-link">YouTube Connection</a> page click <strong>Connect again</strong> and allow it. Until then new playlists cannot be made.</div></div>
    @endif

    @if($problem)
        <div class="alert alert-danger rounded-3"><div><strong>The playlists could not be read from YouTube.</strong> {{ $problem }}</div></div>
    @endif

    <div class="card-box">
        <h3 class="section-title"><i class="bi bi-info-circle text-primary"></i> How it works</h3>
        <p class="mb-2">When a class recording is uploaded, it is added to the playlist of the programme the class belongs to, for example <strong>Diploma in Artificial Intelligence</strong>.</p>
        <ul class="mb-0">
            <li><strong>Automatic</strong> (the default): the LMS looks for a playlist on the channel with exactly that name and uses it. If there is none, it creates it (as Unlisted) the first time it is needed.</li>
            <li>If your playlist has a different name, choose it from the list below and it will be used for that programme.</li>
            <li>A class that covers modules of two programmes is added to both playlists.</li>
        </ul>
    </div>

    <div class="card-box">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <h3 class="section-title mb-0"><i class="bi bi-list-ul text-primary"></i> Playlist for each programme</h3>
            <form method="POST" action="{{ route('admin.youtube.playlists.refresh') }}">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-primary"><i class="bi bi-arrow-repeat"></i> Refresh from YouTube</button>
            </form>
        </div>

        @if($rows->isEmpty())
            <div class="text-muted">There are no programmes yet.</div>
        @else
        <form method="POST" action="{{ route('admin.youtube.playlists.save') }}">
            @csrf
            <div class="table-responsive">
                <table class="table yp-table align-middle">
                    <thead><tr><th>Programme</th><th>Playlist used</th></tr></thead>
                    <tbody>
                        @foreach($rows as $r)
                            <tr>
                                <td><strong>{{ $r['title'] }}</strong></td>
                                <td>
                                    <select name="map[{{ $r['level_id'] }}]" class="form-select">
                                        <option value="" @selected(! $r['saved'])>Automatic: use "{{ $r['title'] }}"{{ $r['match'] ? ' (found on YouTube)' : ' (will be created)' }}</option>
                                        @foreach($channel as $p)
                                            <option value="{{ $p['id'] }}" @selected($r['saved'] === $p['id'])>{{ $p['title'] }}</option>
                                        @endforeach
                                    </select>
                                    @if($r['saved'])<div class="yp-note">Saved choice. Pick "Automatic" to go back to the name match.</div>@endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check2-circle"></i> Save playlists</button>
        </form>
        @endif
    </div>

    <div class="card-box">
        <h3 class="section-title"><i class="bi bi-youtube text-primary"></i> Playlists on the channel ({{ count($channel) }})</h3>
        @if(count($channel))
            <ul class="mb-0">
                @foreach($channel as $p)<li>{{ $p['title'] }}</li>@endforeach
            </ul>
        @else
            <div class="text-muted">None found.</div>
        @endif
    </div>
</div>
@endsection
