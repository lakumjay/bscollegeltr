@extends('layouts.appadmin')

@section('content')
<div class="premium-page premium-page--compact sessions-page">
    <section class="premium-page-header">
        <div><span class="premium-eyebrow">Academic settings</span><h1>Academic sessions</h1><p>Choose the institution-wide academic year used for new student, attendance, salary and fee records.</p></div>
        <div class="sessions-header-status"><span>Current session</span><strong>{{ optional($sessions->firstWhere('id', $currentSessionId))->session_name ?? 'Not selected' }}</strong></div>
    </section>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

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
