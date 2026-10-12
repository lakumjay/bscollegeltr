@extends('layouts.appadmin')

@section('content')
<div class="container-fluid premium-page">
    <div class="premium-page-header">
        <div><span class="premium-page-eyebrow">People operations</span><h1 class="premium-page-title">Staff Management</h1><p class="premium-page-subtitle">Manage staff records, attendance and payroll from one directory.</p></div>
        <div class="premium-header-actions">
            <a href="{{ route('admin.staff.salary.payments') }}" class="premium-btn premium-btn-outline"><i class="bx bx-wallet"></i> Salary payments</a>
            <a href="{{ route('admin.staff.attendance.mark') }}" class="premium-btn premium-btn-outline"><i class="bx bx-calendar-check"></i> Attendance</a>
            <a href="{{ route('admin.staff.create') }}" class="premium-btn premium-btn-gold"><i class="bx bx-user-plus"></i> Add staff</a>
        </div>
    </div>
    <div class="premium-card staff-directory-card">
        <div class="premium-card-header"><div><h2>Staff directory</h2><p>Search staff records and open the actions relevant to each member.</p></div></div>
        <div class="premium-card-body">
            @if(session('success'))<div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>@endif
            <div class="table-responsive"><table class="table premium-table js-premium-datatable">
                <thead><tr><th>Staff ID</th><th>Name</th><th>Email</th><th>Role</th><th>Recurring income</th><th>Status</th><th class="premium-action-cell">Action</th></tr></thead>
                <tbody>@foreach($staff as $member)<tr>
                    <td><span class="premium-number">{{ $member->staff_id }}</span></td><td><span class="premium-name">{{ $member->first_name }} {{ $member->last_name }}</span></td><td>{{ $member->email }}</td><td>{{ $member->role }}</td><td>@if($member->salaryStructure){{ $member->salaryStructure->currency }} {{ number_format((float) $member->salaryStructure->basic_pay + (float) $member->salaryStructure->da + (float) $member->salaryStructure->hra + (float) $member->salaryStructure->additional_earnings, 2) }}@else<span class="text-muted">Not configured</span>@endif</td>
                    <td><span class="premium-status {{ strtolower($member->status) === 'active' ? 'premium-status-active' : 'premium-status-inactive' }}">{{ $member->status }}</span></td>
                    <td class="premium-action-cell"><div class="dropup staff-actions-dropdown"><button class="premium-btn premium-btn-outline staff-action-trigger js-staff-actions-toggle" type="button" aria-expanded="false" aria-controls="staffActionMenu{{ $member->id }}">Actions</button><ul class="dropdown-menu dropdown-menu-end shadow border-0" id="staffActionMenu{{ $member->id }}">
                        <li><a class="dropdown-item" href="{{ route('admin.staff.show', $member) }}">View profile</a></li><li><a class="dropdown-item" href="{{ route('admin.staff.edit', $member) }}">Edit staff</a></li><li><a class="dropdown-item" href="{{ route('admin.staff.transfer_certificate', $member) }}">Transfer certificate</a></li><li><a class="dropdown-item" href="{{ route('admin.staff.id_card', $member) }}">ID card</a></li><li><hr class="dropdown-divider"></li><li><form method="post" action="{{ route('admin.staff.destroy', $member) }}">@csrf @method('DELETE')<button type="submit" class="dropdown-item text-danger">Delete staff</button></form></li>
                    </ul></div></td>
                </tr>@endforeach</tbody>
            </table></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var activeStaffActionMenu = null;

    function closeStaffActionMenu() {
        if (!activeStaffActionMenu) return;

        var menu = activeStaffActionMenu.menu;
        menu.classList.remove('show', 'staff-actions-floating');
        menu.removeAttribute('style');
        activeStaffActionMenu.container.appendChild(menu);
        activeStaffActionMenu.button.setAttribute('aria-expanded', 'false');
        activeStaffActionMenu = null;
    }

    document.querySelectorAll('.js-staff-actions-toggle').forEach(function (button) {
        var container = button.closest('.staff-actions-dropdown');
        var menu = container.querySelector('.dropdown-menu');

        button.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();

            if (activeStaffActionMenu && activeStaffActionMenu.menu === menu) {
                closeStaffActionMenu();
                return;
            }

            closeStaffActionMenu();
            document.body.appendChild(menu);
            menu.classList.add('show', 'staff-actions-floating');
            menu.style.position = 'fixed';
            menu.style.visibility = 'hidden';
            menu.style.zIndex = '2000';

            var rect = button.getBoundingClientRect();
            var top = Math.max(8, rect.top - menu.offsetHeight - 8);
            var left = Math.max(8, Math.min(window.innerWidth - menu.offsetWidth - 8, rect.right - menu.offsetWidth));

            menu.style.top = top + 'px';
            menu.style.left = left + 'px';
            menu.style.visibility = 'visible';
            button.setAttribute('aria-expanded', 'true');
            activeStaffActionMenu = { button: button, container: container, menu: menu };
        });
    });

    document.addEventListener('click', closeStaffActionMenu);
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') closeStaffActionMenu();
    });
    window.addEventListener('resize', closeStaffActionMenu);
    window.addEventListener('scroll', closeStaffActionMenu, true);
});
</script>
@endsection
