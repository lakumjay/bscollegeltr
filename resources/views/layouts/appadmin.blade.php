<!doctype html>
<html lang="en" id="htmltag" class="<?php
    if(isset($_COOKIE['theme'])){ echo $_COOKIE['theme']; }
    else { echo "color-sidebar sidebarcolor5"; }
?>">
<?php
if (isset($_POST['reset_password'])) {
    $oldPassword = $_POST['password'];
    $newPassword = $_POST['newpassword'];
    if (Hash::check($oldPassword, Auth::user()->password)) {
        Auth::user()->update(['password' => Hash::make($newPassword)]);
        echo "<script>alert('Password changed successfully');</script>";
    } else {
        echo "<script>alert('Old password is incorrect.');</script>";
    }
}
?>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ url('favicon.svg') }}" type="image/svg+xml" />
    <link rel="alternate icon" href="{{ url('logo.png') }}" type="image/png" />
    <link rel="apple-touch-icon" href="{{ url('logo.png') }}" />
    <link rel="stylesheet" href="{{url('vertical/assets/plugins/notifications/css/lobibox.min.css')}}" />
    <link rel="stylesheet" href="{{url('vertical/assets/plugins/sweetalert2/sweetalert2.min.css')}}" />
    <link rel="stylesheet" href="{{ url('vertical/assets/plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.min.css') }}" />
    <link href="{{url('vertical/assets/plugins/simplebar/css/simplebar.css')}}" rel="stylesheet" />
    <link href="{{url('vertical/assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css')}}" rel="stylesheet" />
    <link href="{{url('vertical/assets/plugins/metismenu/css/metisMenu.min.css')}}" rel="stylesheet" />
    <link href="{{url('vertical/assets/plugins/datatable/css/dataTables.bootstrap5.min.css')}}" rel="stylesheet" />
    <link href="{{url('vertical/assets/css/pace.min.css')}}" rel="stylesheet" />
    <script src="{{url('vertical/assets/js/pace.min.js')}}"></script>
    <link href="{{url('vertical/assets/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{url('vertical/assets/css/bootstrap-extended.css')}}" rel="stylesheet">
    <link href="{{url('vertical/assets/css/app.css')}}" rel="stylesheet">
    <link href="{{url('vertical/assets/css/icons.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{url('vertical/assets/css/dark-theme.css')}}" />
    <link rel="stylesheet" href="{{url('vertical/assets/css/semi-dark.css')}}" />
    <link rel="stylesheet" href="{{url('vertical/assets/css/header-colors.css')}}" />
    @yield('css')
    <link href="{{url('vertical/assets/css/common.css')}}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Pro:wght@400;600&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>{{ $collegeProfile?->college_name ?? config('app.name', 'College Management') }} | College Management</title>

</head>

<!-- Reset Password Modal -->
<form method="post" id="resetPasswordForm">
    @csrf
    <div class="modal fade premium-modal premium-password-modal" id="resetpassword" tabindex="-1" role="dialog" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div><span class="premium-eyebrow">Account security</span><h5 class="modal-title" id="resetPasswordModalLabel"><i class="fa fa-key me-2"></i>Reset password</h5></div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if(isset($password_wrong_err))
                        <div class="alert alert-danger" role="alert"><strong>Error!</strong> Incorrect current password.</div>
                        <script>$(document).ready(function(){ $('#resetpassword').modal('show'); });</script>
                    @endif
                    <div class="form-group reset-password-field">
                        <label class="form-label">Email address</label>
                        <input type="email" class="form-control" value="{{ Auth::user()->email }}" readonly>
                    </div>
                    <div class="form-group reset-password-field">
                        <label class="form-label">Current password <span>*</span></label>
                        <div class="reset-password-control">
                            <input type="password" class="form-control" id="currentPassword" name="password" placeholder="Current password" required>
                            <button type="button" class="password-toggle" data-target="currentPassword" aria-label="Show current password"><i class="fa fa-eye"></i></button>
                        </div>
                    </div>
                    <div class="reset-password-divider"></div>
                    <div class="form-group reset-password-field">
                        <label class="form-label">New password <span>*</span><small>Minimum 8 characters</small></label>
                        <div class="reset-password-control">
                            <input type="password" class="form-control" id="newPassword" name="newpassword" placeholder="Min. 8 characters" required minlength="8">
                            <button type="button" class="password-toggle" data-target="newPassword" aria-label="Show new password"><i class="fa fa-eye"></i></button>
                        </div>
                    </div>
                    <div class="form-group reset-password-field mb-0">
                        <label class="form-label">Confirm new password <span>*</span></label>
                        <div class="reset-password-control">
                            <input type="password" class="form-control" id="confirmPassword" placeholder="Repeat new password" required>
                            <button type="button" class="password-toggle" data-target="confirmPassword" aria-label="Show confirmation password"><i class="fa fa-eye"></i></button>
                        </div>
                        <small class="text-danger d-none" id="confirmError">Passwords do not match.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="premium-btn premium-btn-outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="premium-btn premium-btn-primary" name="reset_password"><i class="fa fa-save"></i> Save changes</button>
                </div>
            </div>
        </div>
    </div>
</form>

<body>
<div class="wrapper">

    <!-- ─── SIDEBAR ─── -->
    <div class="sidebar-wrapper" data-simplebar="true">
        <div class="sidebar-header">
            @if(!empty($collegeProfile?->logo))
            <img src="{{ url($collegeProfile->logo) }}" alt="College Logo" style="max-height:48px;object-fit:contain;">
            @else
            <img src="{{ url('vertical/assets/images/logo-icon.png') }}" alt="Logo" style="max-height:48px;object-fit:contain;">
            @endif
            <div>
                <h4 class="logo-text">{{ $collegeProfile->college_name ?? 'AKS College' }}</h4>
            </div>
            <div class="toggle-icon ms-auto"><i class='bx bx-arrow-to-left'></i></div>
        </div>

        @php($currentUser = Auth::user())

        <ul class="metismenu" id="menu">

            @if($currentUser->hasPermission('dashboard'))
            <li>
                <a href="{{url('home')}}">
                    <div class="parent-icon"><i class="bx bx-home-circle"></i></div>
                    <div class="menu-title">Dashboard</div>
                </a>
            </li>
            @endif

            @if($currentUser->hasPermission('profile'))
            <li>
                <a href="{{url('admin/profile')}}">
                    <div class="parent-icon"><i class="bx bx-book-bookmark"></i></div>
                    <div class="menu-title">Profile</div>
                </a>
            </li>
            @endif

            @if($currentUser->hasPermission('student-management'))
            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class="fa fa-user-graduate"></i></div>
                    <div class="menu-title">Student Management</div>
                </a>
                <ul>
                    <li><a href="{{url('admin/course')}}"><div class="parent-icon"><i class="bx bx-right-arrow-alt"></i></div><div class="menu-title">Courses</div></a></li>
                    <li><a href="{{url('admin/students/strength')}}"><div class="parent-icon"><i class="bx bx-right-arrow-alt"></i></div><div class="menu-title">Students</div></a></li>
                </ul>
            </li>
            @endif

            @if($currentUser->hasPermission('staff-management'))
            <li>
                <a href="{{url('admin/staff')}}">
                    <div class="parent-icon"><i class="bx bx-user-check"></i></div>
                    <div class="menu-title">Staff Management</div>
                </a>
            </li>
            @endif

            @if($currentUser->hasPermission('staff-attendance'))
            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class="bx bx-calendar-check"></i></div>
                    <div class="menu-title">Staff Attendance</div>
                </a>
                <ul>
                    <li><a href="{{ route('admin.staff.attendance.markAttendancePre') }}"><div class="parent-icon"><i class="bx bx-right-arrow-alt"></i></div><div class="menu-title">Mark Attendance</div></a></li>
                    <li><a href="{{ route('admin.staff.attendance.view') }}"><div class="parent-icon"><i class="bx bx-right-arrow-alt"></i></div><div class="menu-title">View Attendance</div></a></li>
                </ul>
            </li>
            @endif

            {{-- Library management is temporarily hidden; routes and permissions remain available. --}}
            {{-- @if($currentUser->hasPermission('library'))
            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class="bx bx-library"></i></div>
                    <div class="menu-title">Library</div>
                </a>
                <ul>
                    <li><a href="{{url('admin/library/categories')}}"><i class="bx bx-right-arrow-alt"></i> Categories</a></li>
                    <li><a href="javascript:;" class="has-arrow"><i class="bx bx-right-arrow-alt"></i> Masters</a>
                        <ul>
                            <li><a href="{{url('admin/library/book')}}"><i class="bx bx-right-arrow-alt"></i> Book Master</a></li>
                            <li><a href="{{url('admin/library/borrower')}}"><i class="bx bx-right-arrow-alt"></i> Borrower Master</a></li>
                            <li><a href="{{url('admin/library/rack')}}"><i class="bx bx-right-arrow-alt"></i> Rack Master</a></li>
                            <li><a href="{{url('admin/library/publisher')}}"><i class="bx bx-right-arrow-alt"></i> Publisher Master</a></li>
                            <li><a href="{{url('admin/library/author')}}"><i class="bx bx-right-arrow-alt"></i> Author Master</a></li>
                        </ul>
                    </li>
                    <li><a href="{{url('admin/library/issue_book?issue')}}"><i class="bx bx-right-arrow-alt"></i> Issue Book</a></li>
                    <li><a href="{{url('admin/library/issue_book?recieve')}}"><i class="bx bx-right-arrow-alt"></i> Collect Book</a></li>
                    <li><a href="{{url('admin/library/issue_book?return')}}"><i class="bx bx-right-arrow-alt"></i> Book Issue Report</a></li>
                </ul>
            </li>
            @endif --}}

            @if($currentUser->hasPermission('salary-management'))
            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class="fa fa-wallet"></i></div>
                    <div class="menu-title">Salary Management</div>
                </a>
                <ul>
                    <li><a href="{{ route('admin.staff.salary.payments') }}"><div class="parent-icon"><i class="bx bx-right-arrow-alt"></i></div><div class="menu-title">Manage Payments</div></a></li>
                    <li><a href="{{ route('admin.staff.salary.payments_view') }}"><div class="parent-icon"><i class="bx bx-right-arrow-alt"></i></div><div class="menu-title">View Salary</div></a></li>
                    <li><a href="{{ route('admin.staff.salary.report') }}"><div class="parent-icon"><i class="bx bx-right-arrow-alt"></i></div><div class="menu-title">Salary Report</div></a></li>
                </ul>
            </li>
            @endif

            @if($currentUser->hasPermission('fee-management'))
            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class="fa fa-file-invoice-dollar"></i></div>
                    <div class="menu-title">Fee Management</div>
                </a>
                <ul>
                    <li><a href="{{url('admin/fee/structure')}}"><div class="parent-icon"><i class="bx bx-right-arrow-alt"></i></div><div class="menu-title">Structure</div></a></li>
                                {{-- Special-category pricing retired in favour of per-payment discounts. --}}
                    <li><a href="{{url('admin/fee/management')}}"><div class="parent-icon"><i class="bx bx-right-arrow-alt"></i></div><div class="menu-title">Fee Management</div></a></li>
                    <li><a href="{{url('admin/fee/report')}}"><div class="parent-icon"><i class="bx bx-right-arrow-alt"></i></div><div class="menu-title">Report</div></a></li>
                </ul>
            </li>
            @endif

            @if($currentUser->hasPermission('accounts'))
            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class="bx bx-calculator"></i></div>
                    <div class="menu-title">Accounts</div>
                </a>
                <ul>
                    <li><a href="{{url('admin/accounts/income')}}"><div class="parent-icon"><i class="bx bx-right-arrow-alt"></i></div><div class="menu-title">Income</div></a></li>
                    <li><a href="{{url('admin/accounts/expense')}}"><div class="parent-icon"><i class="bx bx-right-arrow-alt"></i></div><div class="menu-title">Expense</div></a></li>
                    <li><a href="{{url('admin/accounts/daybook')}}"><div class="parent-icon"><i class="bx bx-right-arrow-alt"></i></div><div class="menu-title">DCR</div></a></li>
                </ul>
            </li>
            @endif

            {{-- Exam management is temporarily hidden; routes and permissions remain available. --}}
            {{-- @if($currentUser->hasPermission('exam-management'))
            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class="fa fa-pen-to-square"></i></div>
                    <div class="menu-title">Exam Management</div>
                </a>
                <ul>
                    <li><a href="{{ url('admin/grading-scale') }}"><div class="parent-icon"><i class="bx bx-right-arrow-alt"></i></div><div class="menu-title">Grading Scale</div></a></li>
                    <li><a href="{{ url('admin/exams') }}"><div class="parent-icon"><i class="bx bx-right-arrow-alt"></i></div><div class="menu-title">Manage Exams</div></a></li>
                    <li><a href="{{ url('admin/marks-entry') }}"><div class="parent-icon"><i class="bx bx-right-arrow-alt"></i></div><div class="menu-title">Marks Entry</div></a></li>
                    <li><a href="{{ url('admin/report-cards') }}"><div class="parent-icon"><i class="bx bx-right-arrow-alt"></i></div><div class="menu-title">Report Cards</div></a></li>
                </ul>
            </li>
            @endif --}}

            @if($currentUser->hasPermission('inventory-management'))
            <li class="{{ Request::is('admin/inventory*') || Request::is('admin/stock*') ? 'mm-active' : '' }}">
                <a href="{{ url('admin/inventory') }}">
                    <div class="parent-icon"><i class="bx bx-package"></i></div>
                    <div class="menu-title">Stock / Inventory</div>
                </a>
            </li>
            @endif

            @if($currentUser->hasPermission('session-management'))
            <li>
                <a href="{{url('admin/sessions')}}">
                    <div class="parent-icon"><i class="bx bx-time-five"></i></div>
                    <div class="menu-title">Session Mgt.</div>
                </a>
            </li>
            @endif

            @if($currentUser->isAdmin())
            <li>
                <a href="{{url('admin/users')}}">
                    <div class="parent-icon"><i class="bx bx-shield-quarter"></i></div>
                    <div class="menu-title">User &amp; Permissions</div>
                </a>
            </li>
            <li>
                <a href="{{url('admin/activity-logs')}}">
                    <div class="parent-icon"><i class="bx bx-history"></i></div>
                    <div class="menu-title">Activity History</div>
                </a>
            </li>
            @endif

        </ul>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-danger w-100">
                <i class="fa fa-sign-out-alt me-2"></i> Logout
            </button>
        </form>
    </div>
    <!-- ─── END SIDEBAR ─── -->

    <!-- ─── HEADER ─── -->
    <header class="admin-top-header">
        <div class="topbar d-flex align-items-center">
            <nav class="navbar navbar-expand" style="width:100%;padding:0;">
                <div class="mobile-toggle-menu"><i class='bx bx-menu'></i></div>

                <div class="ms-auto d-flex align-items-center gap-3">
                    <?php $date = date('d M l'); ?>

                    <!-- Date / Time Pill -->
                    <div class="date-time-pill">
                        <span class="pill-date">📅 <?= $date ?></span>
                        <div class="pill-divider"></div>
                        <span id="live-time">--:--:--</span>
                        <button class="btn-holiday" onclick="showHolidays()">Holidays</button>
                    </div>

                    <!-- Session badge -->
                    <button class="session-badge" onclick="window.location='{{url('admin/sessions')}}'">
                        <i class="bx bx-calendar-alt me-1"></i>
                        Session: {{ Auth::user()->session->session_name ?? 'N/A' }}
                    </button>

                    <!-- User dropdown -->
                    <div class="user-box dropdown">
                        <a class="d-flex align-items-center nav-link dropdown-toggle dropdown-toggle-nocaret" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-avatar">
                                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                            </div>
                            <div class="user-info ps-2">
                                <p class="user-name mb-0">{{ Auth::user()->name }}</p>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{url('/admin/profile')}}"><i class="bx bx-user"></i> Profile</a></li>
                            <li><a class="dropdown-item" href="javascript:;" data-bs-toggle="modal" data-bs-target="#resetpassword"><i class="bx bx-key"></i> Change Password</a></li>
                            <li><hr class="dropdown-divider" style="margin:0.3rem 0;"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bx bx-log-out-circle"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </header>
    <!-- ─── END HEADER ─── -->

    <!-- ─── PAGE CONTENT ─── -->
    <div class="page-wrapper">
        <div class="page-content">
            @yield('content')
        </div>
    </div>

    <!-- Overlays -->
    <div class="overlay toggle-icon"></div>
    <a href="javascript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>

    <!-- Footer -->
    <footer class="page-footer">
        <p class="mb-0">Copyright &copy; {{ date('Y') }} &nbsp;|&nbsp; {{ $collegeProfile?->college_name ?? config('app.name', 'College Management System') }}</p>
    </footer>

</div><!-- end wrapper -->

<!-- Holiday Modal -->
<div id="holiday-overlay" onclick="hideHolidays()"></div>
<div id="holiday-modal">
    <h2>📅 Upcoming Holidays</h2>
    <ul>
        <li><span>🌱</span><span>5 June – World Environment Day</span></li>
        <li><span>🕌</span><span>17 June – Eid al-Adha</span></li>
        <li><span>🇮🇳</span><span>15 August – Independence Day</span></li>
        <li><span>🕊️</span><span>2 October – Gandhi Jayanti</span></li>
        <li><span>🎄</span><span>25 December – Christmas</span></li>
    </ul>
    <div style="text-align:right;">
        <button onclick="hideHolidays()" style="background:#0b1f3a;color:#fff;border:none;padding:7px 16px;border-radius:8px;font-size:0.82rem;cursor:pointer;">Close</button>
    </div>
</div>

@if(session('success'))
<script>
window.addEventListener('load', function () {
    const message = @json(session('success'));
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: message,
            showConfirmButton: false,
            timer: 2600,
            timerProgressBar: true
        });
    } else if (typeof success_noti2 === 'function') {
        success_noti2(message);
    }
});
</script>
@endif

<!-- Scripts -->
<script src="{{url('vertical/assets/js/jquery.min.js')}}"></script>
<script src="{{url('vertical/assets/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{url('vertical/assets/plugins/simplebar/js/simplebar.min.js')}}"></script>
<script src="{{url('vertical/assets/plugins/metismenu/js/metisMenu.min.js')}}"></script>
<script src="{{url('vertical/assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js')}}"></script>
<script src="{{url('vertical/assets/plugins/datatable/js/jquery.dataTables.min.js')}}"></script>
<script src="{{url('vertical/assets/plugins/datatable/js/dataTables.bootstrap5.min.js')}}"></script>
<script src="{{url('vertical/assets/plugins/notifications/js/lobibox.min.js')}}"></script>
<script src="{{url('vertical/assets/plugins/notifications/js/notifications.min.js')}}"></script>
<script src="{{url('vertical/assets/plugins/notifications/js/notification-custom-script.js')}}"></script>
<script src="{{url('vertical/assets/plugins/sweetalert2/sweetalert2.all.min.js')}}"></script>
<script src="{{ url('vertical/assets/plugins/bootstrap-material-datetimepicker/js/moment.min.js') }}"></script>
<script src="{{ url('vertical/assets/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.min.js') }}"></script>
<script src="{{ url('vertical/assets/js/premium-datepicker.js') }}"></script>
<script src="{{url('vertical/assets/plugins/chartjs/js/Chart.min.js')}}"></script>
<script src="{{url('vertical/assets/plugins/chartjs/js/Chart.extension.js')}}"></script>
<script src="{{url('vertical/assets/js/app.js')}}"></script>

<script>
    // Live time
    function updateTime() {
        const t = new Date().toLocaleTimeString('en-IN', { hour:'2-digit', minute:'2-digit', second:'2-digit' });
        const el = document.getElementById('live-time');
        if(el) el.textContent = t;
    }
    setInterval(updateTime, 1000);
    updateTime();

    // Holidays
    function showHolidays() {
        document.getElementById('holiday-modal').style.display = 'block';
        document.getElementById('holiday-overlay').style.display = 'block';
    }
    function hideHolidays() {
        document.getElementById('holiday-modal').style.display = 'none';
        document.getElementById('holiday-overlay').style.display = 'none';
    }

    // DataTables
    $(document).ready(function() {
        if($('#example').length) $('#example').DataTable();
        if($('#example2').length && !$.fn.DataTable.isDataTable('#example2')) {
            $('#example2').DataTable({ lengthChange: false });
        }

        $('.js-premium-datatable').each(function() {
            if (!$.fn.DataTable.isDataTable(this)) {
                $(this).DataTable({
                    dom: '<"row align-items-center mb-3"<"col-md-6"l><"col-md-6"f>>rt<"row align-items-center mt-3"<"col-md-6"i><"col-md-6"p>>',
                    pageLength: 10,
                    lengthMenu: [[10, 25, 50], [10, 25, 50]],
                    autoWidth: false,
                    language: { search: '', searchPlaceholder: 'Search records...', lengthMenu: 'Show _MENU_ entries' }
                });
            }
        });
    });

    // Shared destructive-action confirmation. Existing delete endpoints and CSRF
    // forms stay unchanged; only the browser confirmation UI is standardized.
    document.addEventListener('submit', function (event) {
        const form = event.target;
        if (!(form instanceof HTMLFormElement) || form.dataset.deleteConfirmed === 'true') return;
        if (form.matches('.course-delete-form, .delete-subject-form')) return;

        const method = (form.querySelector('input[name="_method"]')?.value || form.method || '').toLowerCase();
        const isDeleteForm = method === 'delete' || /\/delete(?:\/|$|\?)/i.test(form.action) || /delete/i.test(form.className);
        if (!isDeleteForm) return;

        event.preventDefault();
        event.stopImmediatePropagation();
        const proceed = function () {
            form.dataset.deleteConfirmed = 'true';
            form.submit();
        };
        if (typeof Swal === 'undefined') {
            if (window.confirm('Delete this record? This action cannot be undone.')) proceed();
            return;
        }
        Swal.fire({
            title: 'Delete this record?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#b44955',
            cancelButtonColor: '#667085'
        }).then(function (result) { if (result.isConfirmed) proceed(); });
    }, true);

    document.addEventListener('click', function (event) {
        const link = event.target.closest('a[href]');
        if (!link || link.dataset.deleteConfirmed === 'true') return;
        const looksDestructive = /\/delete(?:\/|$|\?)/i.test(link.href) || /\bdelete\b/i.test(link.textContent || '');
        if (!looksDestructive) return;

        event.preventDefault();
        event.stopImmediatePropagation();
        const proceed = function () { link.dataset.deleteConfirmed = 'true'; window.location.assign(link.href); };
        if (typeof Swal === 'undefined') {
            if (window.confirm('Delete this record? This action cannot be undone.')) proceed();
            return;
        }
        Swal.fire({
            title: 'Delete this record?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#b44955',
            cancelButtonColor: '#667085'
        }).then(function (result) { if (result.isConfirmed) proceed(); });
    }, true);

    // Password toggle
    $(document).ready(function() {
        $('.premium-password-modal .password-toggle').on('click', function() {
            var input = document.getElementById(this.dataset.target);
            var icon = this.querySelector('i');
            if (!input) return;
            input.type = input.type === 'password' ? 'text' : 'password';
            icon.className = input.type === 'password' ? 'fa fa-eye' : 'fa fa-eye-slash';
        });
        $('#resetPasswordForm').on('submit', function(e) {
            if($('#newPassword').val() !== $('#confirmPassword').val()) {
                e.preventDefault();
                $('#confirmError').removeClass('d-none');
            }
        });
    });
</script>


@yield('scripts')
</body>
</html>
