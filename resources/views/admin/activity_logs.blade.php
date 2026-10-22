@extends('layouts.appadmin')

@section('content')
<div class="container-fluid premium-page activity-log-page">
    <div class="premium-page-header">
        <div><span class="premium-page-eyebrow">System monitoring</span><h1 class="premium-page-title">Activity History</h1><p class="premium-page-subtitle">Review logged user actions, requests and submitted data.</p></div>
        <div class="premium-header-actions"><a href="{{ url('admin/activity-logs') }}" class="premium-btn premium-btn-outline"><i class="bx bx-refresh"></i> Reset filters</a></div>
    </div>

    <div class="premium-card mb-3"><div class="premium-card-header"><div><h2>Filter activity</h2><p>Narrow the history by date, user or module.</p></div></div><div class="premium-card-body"><form method="GET" action="{{ url('admin/activity-logs') }}" class="premium-form-grid activity-filter-form">
        <div class="premium-field"><label for="activity_date" class="premium-label">Date</label><input type="text" name="date" id="activity_date" class="form-control premium-datepicker" value="{{ request('date') ? \Carbon\Carbon::parse(request('date'))->format('d-m-Y') : '' }}" autocomplete="off" placeholder="DD-MM-YYYY"></div>
        <div class="premium-field"><label for="activity_user" class="premium-label">User</label><select name="user_id" id="activity_user" class="form-select"><option value="">All users</option>@foreach($users as $user)<option value="{{ $user->id }}" @selected((string) request('user_id') === (string) $user->id)>{{ $user->name }}{{ $user->type === 'admin' ? ' (Admin)' : '' }}</option>@endforeach</select></div>
        <div class="premium-field"><label for="activity_module" class="premium-label">Module</label><select name="module" id="activity_module" class="form-select"><option value="">All modules</option>@foreach($modules as $module)<option value="{{ $module }}" @selected(request('module') === $module)>{{ str_replace('_', ' ', $module) }}</option>@endforeach</select></div>
        <div class="premium-field d-flex align-items-end"><button type="submit" class="premium-btn premium-btn-gold"><i class="bx bx-filter-alt"></i> Apply filters</button></div>
    </form></div></div>

    <div class="activity-stat-grid mb-3">
        <div class="activity-stat"><i class="bx bx-list-ul"></i><div><strong>{{ $activityStats['total'] }}</strong><span>Matching records</span></div></div>
        <div class="activity-stat"><i class="bx bx-calendar-check"></i><div><strong>{{ $activityStats['today'] }}</strong><span>Today</span></div></div>
        <div class="activity-stat"><i class="bx bx-user-check"></i><div><strong>{{ $activityStats['users'] }}</strong><span>Active users</span></div></div>
        <div class="activity-stat"><i class="bx bx-trending-up"></i><div><strong>{{ $activityStats['month'] }}</strong><span>This month</span></div></div>
    </div>

    <div class="premium-card"><div class="premium-card-header"><div><h2>Activity log</h2><p>{{ $logs->total() }} total record{{ $logs->total() === 1 ? '' : 's' }}{{ request('date') ? ' for ' . \Carbon\Carbon::parse(request('date'))->format('d-m-Y') : '' }}.</p></div><div class="premium-table-search"><i class="bx bx-search"></i><input type="search" id="quickSearch" aria-label="Search activity records" placeholder="Search displayed records"></div></div>
        <div class="premium-card-body p-0"><div class="table-responsive"><table class="table premium-table activity-log-table"><thead><tr><th>Date &amp; time</th><th>User</th><th>Module</th><th>Action</th><th>Details summary</th><th>IP address</th><th class="premium-action-cell">Action</th></tr></thead><tbody>
            @forelse($logs as $log)
                <?php $requestData = $log->request_data ? json_decode($log->request_data, true) : []; ?>
                <tr>
                    <td><strong class="premium-name">{{ $log->created_at->format('d-m-Y') }}</strong><small class="activity-log-time">{{ $log->created_at->format('h:i:s A') }}</small></td>
                    <td><span class="activity-user" title="{{ $log->user_name ?: 'System' }}">{{ $log->user_name ?: 'System' }}</span></td>
                    <td><span class="activity-module">{{ str_replace('_', ' ', $log->module) }}</span></td>
                    <td><span class="activity-action" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $log->action }}">{{ $log->action }}</span></td>
                    <td class="activity-description"><span class="activity-description-text" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $log->description ?: '—' }}">{{ $log->description ?: '—' }}</span></td>
                    <td class="activity-ip">{{ $log->ip_address ?: '—' }}</td>
                    <td class="premium-action-cell"><button type="button" class="premium-btn premium-btn-outline premium-btn-sm btn-view-details" data-user="{{ $log->user_name ?: 'System' }}" data-action="{{ $log->action }}" data-module="{{ str_replace('_', ' ', $log->module) }}" data-date="{{ $log->created_at->format('d-m-Y, h:i:s A') }}" data-ip="{{ $log->ip_address ?: '—' }}" data-method="{{ $log->method ?: '—' }}" data-url="{{ $log->url ?: '—' }}" data-description="{{ $log->description }}" data-fields="{{ htmlspecialchars(json_encode($requestData, JSON_UNESCAPED_UNICODE), ENT_QUOTES) }}"><i class="bx bx-show"></i> Details</button></td>
                </tr>
            @empty
                <tr><td colspan="7" class="activity-empty-cell"><div class="premium-empty-state"><i class="bx bx-info-circle"></i><h3>No activity records match these filters.</h3><p>Try another date, user, or module filter.</p></div></td></tr>
            @endforelse
        </tbody></table></div></div>
        <div class="premium-pagination activity-log-pagination d-flex justify-content-between align-items-center"><span class="premium-muted">@if($logs->total()) Showing {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ $logs->total() }} records @else No activity records @endif</span>@if($logs->hasPages()){{ $logs->links() }}@endif</div>
    </div>
</div>

<div class="modal fade premium-modal" id="detailModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-lg modal-dialog-scrollable"><div class="modal-content"><div class="modal-header"><div><span class="premium-eyebrow">Audit record</span><h5>Activity details</h5></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body"><div class="activity-detail-grid"><div><span>User</span><strong id="dtl-user">—</strong></div><div><span>Module</span><strong id="dtl-module">—</strong></div><div><span>Action</span><strong id="dtl-action">—</strong></div><div><span>Date &amp; time</span><strong id="dtl-date">—</strong></div><div><span>Method</span><strong id="dtl-method">—</strong></div><div><span>IP address</span><strong id="dtl-ip">—</strong></div></div><div class="activity-detail-section"><span>Summary</span><p id="dtl-description">—</p></div><div class="activity-detail-section"><span>Request URL</span><p id="dtl-url" class="activity-url">—</p></div><div class="activity-detail-section"><span>Submitted data</span><div class="table-responsive"><table class="table premium-table activity-field-table" id="dtl-fields-table"><thead><tr><th>Field</th><th>Value</th></tr></thead><tbody id="dtl-fields-body"></tbody></table></div><p id="dtl-no-fields" class="premium-muted mb-0" hidden>No submitted data was recorded for this action.</p></div></div><div class="modal-footer"><button type="button" class="premium-btn premium-btn-outline" data-bs-dismiss="modal">Close</button></div></div></div></div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
document.getElementById('quickSearch')?.addEventListener('input', function () { const term = this.value.toLowerCase(); document.querySelectorAll('.activity-log-table tbody tr').forEach((row) => row.style.display = row.textContent.toLowerCase().includes(term) ? '' : 'none'); });
document.addEventListener('click', function (event) { const button = event.target.closest('.btn-view-details'); if (!button) return; const get = (name) => button.getAttribute('data-' + name) || '—'; ['user', 'module', 'action', 'date', 'method', 'ip', 'description', 'url'].forEach((name) => { const element = document.getElementById('dtl-' + name); if (element) element.textContent = get(name); }); const body = document.getElementById('dtl-fields-body'); const table = document.getElementById('dtl-fields-table'); const empty = document.getElementById('dtl-no-fields'); body.innerHTML = ''; let fields = {}; try { fields = JSON.parse(button.getAttribute('data-fields') || '{}'); } catch (_) {} const entries = Object.entries(fields).filter(([, value]) => value !== null && value !== '' && value !== undefined); table.hidden = entries.length === 0; empty.hidden = entries.length !== 0; entries.forEach(([key, value]) => { const row = document.createElement('tr'); const field = document.createElement('td'); const fieldName = document.createElement('strong'); fieldName.textContent = key.replace(/_/g, ' ').replace(/\b\w/g, (letter) => letter.toUpperCase()); field.appendChild(fieldName); const fieldKey = document.createElement('small'); fieldKey.className = 'activity-field-key'; fieldKey.textContent = key; field.appendChild(fieldKey); const fieldValue = document.createElement('td'); fieldValue.textContent = Array.isArray(value) || typeof value === 'object' ? JSON.stringify(value) : value; row.append(field, fieldValue); body.appendChild(row); }); bootstrap.Modal.getOrCreateInstance(document.getElementById('detailModal')).show(); });
</script>
@endsection
