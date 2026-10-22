@extends('layouts.appadmin')

@section('content')
<div class="premium-page premium-page--compact sessions-page">
    <section class="premium-page-header">
        <div><span class="premium-eyebrow">Academic settings</span><h1>Academic sessions</h1><p>Choose the institution-wide academic year used for new student, attendance, salary and fee records.</p></div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <div class="sessions-header-status">
                <span>Current session</span><strong>{{ optional($sessions->firstWhere('id', $currentSessionId))->session_name ?? 'Not selected' }}</strong>
            </div>
            <form action="{{ route('admin.sessions.merge') }}" method="POST" class="d-inline" onsubmit="return confirm('Do you want to automatically merge equivalent duplicate sessions (e.g. 2026-2030 into 2026-30)?');">
                @csrf
                <button type="submit" class="btn btn-warning btn-sm text-dark fw-bold px-3 py-1 shadow-sm" style="font-size: 12px; border-radius: 6px; letter-spacing: 0.2px;">
                    Clean Duplicates
                </button>
            </form>
        </div>
    </section>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif
    @if($errors->any()) <div class="alert alert-danger">{{ $errors->first() }}</div> @endif

    <section class="premium-content-card sessions-directory">
        <div class="premium-card-heading"><div><span class="premium-eyebrow">Available years</span><h2>Select an academic session</h2></div><span class="premium-count-badge">{{ $sessions->count() }} sessions</span></div>
        <div class="sessions-grid">
            @forelse($sessions as $session)
                @php($isActive = (int) $currentSessionId === (int) $session->id)
                <article class="session-tile {{ $isActive ? 'is-active' : '' }}">
                    <div class="session-tile__content">
                        <span class="premium-eyebrow">Academic session</span>
                        <h3 class="mb-1">{{ $session->session_name }}</h3>
                        <span class="session-status {{ $isActive ? 'is-active' : '' }}">{{ $isActive ? 'Currently active' : 'Available' }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        @if($isActive)
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size: 11px; font-weight: 600; border-radius: 6px;">Active</span>
                        @else
                            <form method="POST" action="{{ route('admin.sessions.activate', $session->id) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-dark px-2 py-1" style="font-size: 11px; font-weight: 600; border-radius: 6px;">Activate</button>
                            </form>
                        @endif
                        <button type="button" class="btn btn-sm btn-outline-secondary px-2 py-1" style="font-size: 11px; font-weight: 600; border-radius: 6px;" title="Edit Session" onclick="editSessionName({{ $session->id }}, '{{ addslashes($session->session_name) }}')">
                            Edit
                        </button>
                    </div>
                </article>
            @empty
                <div class="premium-empty-state"><h3>No academic sessions available</h3><p>Create a session before selecting an active academic year.</p></div>
            @endforelse
        </div>
    </section>
</div>

{{-- Edit Session Modal --}}
<div class="modal fade" id="editSessionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow border-0" style="border-radius: 12px;">
            <form id="editSessionForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Edit Academic Session</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-3">
                    <label class="form-label small fw-semibold text-secondary mb-1">Session Name</label>
                    <input type="text" name="session_name" id="editSessionInput" class="form-control" placeholder="e.g. 2026-30" required>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-3">Update Session</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function editSessionName(id, name) {
    document.getElementById('editSessionForm').action = '{{ url("admin/sessions") }}/' + id;
    document.getElementById('editSessionInput').value = name;
    var modal = new bootstrap.Modal(document.getElementById('editSessionModal'));
    modal.show();
}
</script>
@endsection
