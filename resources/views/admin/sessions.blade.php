@extends('layouts.appadmin')

@section('content')
<div class="premium-page premium-page--compact sessions-page">
    <section class="premium-page-header">
        <div><span class="premium-eyebrow">Academic settings</span><h1>Academic sessions</h1><p>Choose the institution-wide academic year used for new student, attendance, salary and fee records.</p></div>
        <div class="sessions-header-status">
            <div>
                <span>Current session</span><strong>{{ optional($sessions->firstWhere('id', $currentSessionId))->session_name ?? 'Not selected' }}</strong>
            </div>
            <button type="button" class="btn btn-primary btn-sm ms-3" data-bs-toggle="collapse" data-bs-target="#createSessionBox" style="font-weight: 600;">
                <i class="fa fa-plus me-1"></i> Add Session
            </button>
        </div>
    </section>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if($errors->any()) <div class="alert alert-danger">{{ $errors->first() }}</div> @endif

    <div class="collapse mb-4" id="createSessionBox">
        <div class="card card-body shadow-sm border-0" style="background: #ffffff; border-radius: 12px; padding: 20px;">
            <h6 class="fw-bold mb-3"><i class="fa fa-calendar-plus-o text-primary me-2"></i> Add New Academic Session</h6>
            <form action="{{ route('admin.sessions.store') }}" method="POST" class="row g-3 align-items-end">
                @csrf
                <div class="col-md-5">
                    <label class="form-label small fw-semibold text-secondary mb-1">Session Name / Year</label>
                    <input type="text" name="session_name" class="form-control" placeholder="e.g. 2026-2030 or 2026-30" required>
                    <small class="text-muted">Enter the 4-year or custom academic session format.</small>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-success px-4 w-100" style="height: 38px;" onclick="if(this.form.checkValidity()){this.disabled=true;this.innerHTML='<i class=\'fa fa-spinner fa-spin me-1\'></i> Saving...';this.form.submit();}">
                        <i class="fa fa-save me-1"></i> Save Session
                    </button>
                </div>
            </form>
        </div>
    </div>

    <section class="premium-content-card sessions-directory">
        <div class="premium-card-heading"><div><span class="premium-eyebrow">Available years</span><h2>Select an academic session</h2></div><span class="premium-count-badge">{{ $sessions->count() }} sessions</span></div>
        <div class="sessions-grid">
            @forelse($sessions as $session)
                @php($isActive = (int) $currentSessionId === (int) $session->id)
                <article class="session-tile {{ $isActive ? 'is-active' : '' }}">
                    <div class="session-tile__icon"><i class="fa fa-calendar"></i></div>
                    <div class="session-tile__content"><span class="premium-eyebrow">Academic session</span><h3>{{ $session->session_name }}</h3><span class="session-status {{ $isActive ? 'is-active' : '' }}"><i class="fa fa-circle"></i> {{ $isActive ? 'Currently active' : 'Available' }}</span></div>
                    @if($isActive)
                        <span class="premium-btn premium-btn-outline premium-btn-sm is-static"><i class="fa fa-check"></i> Active</span>
                    @else
                        <form method="POST" action="{{ route('admin.sessions.activate', $session->id) }}">@csrf<button type="submit" class="premium-btn premium-btn-primary premium-btn-sm"><i class="fa fa-power-off"></i> Activate</button></form>
                    @endif
                </article>
            @empty
                <div class="premium-empty-state"><i class="fa fa-calendar-o"></i><h3>No academic sessions available</h3><p>Create a session before selecting an active academic year.</p></div>
            @endforelse
        </div>
    </section>
</div>
@endsection
