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
    <style>
    /* Topbar Search & Shortcut Badges */
    .topbar-cmd-btn {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        height: 38px;
        padding: 0 14px;
        font-size: 0.82rem;
        color: #64748b;
        display: inline-flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        transition: all 0.2s ease;
        margin-left: 15px;
        width: 290px;
        white-space: nowrap;
    }
    .topbar-cmd-btn:hover {
        background: #ffffff;
        border-color: #cbd5e1;
        color: #1e293b;
        box-shadow: 0 3px 12px rgba(15, 23, 42, 0.08);
    }
    .topbar-cmd-btn .cmd-btn-text {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        font-weight: 500;
        color: #64748b;
    }
    .cmd-kbd-badge {
        background: #e2e8f0;
        border: 1px solid #cbd5e1;
        border-radius: 5px;
        padding: 2px 6px;
        font-size: 0.70rem;
        font-weight: 700;
        font-family: inherit;
        color: #475569;
        letter-spacing: 0.3px;
        box-shadow: inset 0 -1px 0 rgba(0,0,0,0.12);
        white-space: nowrap;
        flex-shrink: 0;
        line-height: 1.2;
        display: inline-flex;
        align-items: center;
    }
    .btn-shortcuts-badge {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        height: 38px;
        padding: 0 13px;
        font-size: 0.81rem;
        color: #475569;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    .btn-shortcuts-badge:hover {
        background: #0b1f3a;
        border-color: #0b1f3a;
        color: #f8e6a4;
    }
    .btn-shortcuts-badge:hover .cmd-kbd-badge {
        background: rgba(255, 255, 255, 0.15);
        border-color: rgba(255, 255, 255, 0.3);
        color: #f8e6a4;
    }
    .session-badge {
        height: 38px !important;
        display: inline-flex !important;
        align-items: center !important;
        border-radius: 20px !important;
        padding: 0 14px !important;
        font-size: 0.82rem !important;
        white-space: nowrap;
    }
    /* Command Palette Item Styles */
    .cmd-nav-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        border-radius: 10px;
        text-decoration: none !important;
        color: #1e293b;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        transition: all 0.15s ease;
    }
    .cmd-nav-item:hover {
        background: #eff6ff;
        border-color: #93c5fd;
        color: #1d4ed8;
        transform: translateY(-1px);
    }
    .cmd-icon-box {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }
    /* Floating Speed Dial */
    .floating-speed-dial {
        position: fixed;
        bottom: 24px;
        right: 24px;
        z-index: 1040;
    }
    .speed-dial-main {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, #0b1f3a, #1e3d6e);
        color: #fff;
        border: none;
        box-shadow: 0 4px 18px rgba(11,31,58,0.35);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        cursor: pointer;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .speed-dial-main:hover {
        transform: scale(1.08);
        box-shadow: 0 6px 24px rgba(11,31,58,0.45);
    }
    .speed-dial-menu {
        position: absolute;
        bottom: 60px;
        right: 0;
        display: none;
        flex-direction: column;
        gap: 9px;
        align-items: flex-end;
    }
    .speed-dial-menu.show {
        display: flex;
    }
    .speed-dial-item {
        display: flex;
        align-items: center;
        gap: 8px;
        text-decoration: none !important;
        background: #fff;
        padding: 6px 12px;
        border-radius: 20px;
        box-shadow: 0 3px 12px rgba(0,0,0,0.12);
        font-size: 0.81rem;
        font-weight: 600;
        color: #1e293b;
        white-space: nowrap;
        transition: transform 0.18s ease;
    }
    .speed-dial-item:hover {
        transform: translateX(-4px);
        background: #f8fafc;
        color: #2563eb;
    }
    .speed-dial-item i {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
    }
    </style>
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

        <?php $currentUser = Auth::user(); ?>

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

                <!-- Global Command Search Trigger with Visible Kbd Badge -->
                <button type="button" class="topbar-cmd-btn d-none d-sm-flex align-items-center" onclick="openGlobalSearch()" title="Search anything... (Ctrl + K)">
                    <div class="d-flex align-items-center gap-2 overflow-hidden me-2">
                        <i class="bx bx-search text-muted" style="font-size: 1.15rem; flex-shrink: 0;"></i>
                        <span class="cmd-btn-text">Search student, fee, or page...</span>
                    </div>
                    <kbd class="cmd-kbd-badge">Ctrl K</kbd>
                </button>

                <div class="ms-auto d-flex align-items-center gap-2 gap-md-3">
                    <!-- Mobile Search Icon -->
                    <button type="button" class="btn btn-sm btn-light d-sm-none" onclick="openGlobalSearch()" title="Search (Ctrl + K)">
                        <i class="bx bx-search"></i>
                    </button>

                    <!-- Keyboard Shortcuts Quick Helper Button with Visible Badge -->
                    <button type="button" class="btn-shortcuts-badge" onclick="openShortcutsModal()" title="Keyboard Shortcuts (Press ?)">
                        <i class="fa fa-keyboard text-muted me-1"></i>
                        <span class="d-none d-md-inline fw-semibold small">Shortcuts</span>
                        <kbd class="cmd-kbd-badge ms-1">?</kbd>
                    </button>

                    <!-- Session Switcher Dropdown with Visible Badge -->
                    <?php
                        $userSession = Auth::user()->session;
                        $sessionName = $userSession ? $userSession->session_name : 'Not Set';
                        $headerSessions = \App\Models\Session::all()->sort(function($a, $b) {
                            return strnatcasecmp($b->session_name, $a->session_name);
                        });
                    ?>
                    <div class="dropdown">
                        <button class="session-badge dropdown-toggle dropdown-toggle-nocaret d-flex align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Switch Academic Session (Alt + Y)">
                            <i class="bx bx-calendar-alt me-1 text-warning"></i>
                            <span class="d-none d-sm-inline me-1">Session:</span>
                            <strong>{{ $sessionName }}</strong>
                            <i class="bx bx-chevron-down ms-1"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 py-2" style="border-radius: 12px; min-width: 200px;">
                            <li class="dropdown-header small text-uppercase text-muted fw-bold">Select Active Session</li>
                            @foreach($headerSessions as $hs)
                                <li>
                                    <form method="POST" action="{{ route('admin.sessions.activate', $hs->id) }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item d-flex align-items-center justify-content-between py-2 {{ (int)Auth::user()->active_session_id === (int)$hs->id ? 'bg-light text-primary fw-bold' : '' }}">
                                            <span>{{ $hs->session_name }}</span>
                                            @if((int)Auth::user()->active_session_id === (int)$hs->id)
                                                <i class="bx bx-check-circle text-primary"></i>
                                            @endif
                                        </button>
                                    </form>
                                </li>
                            @endforeach
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <a class="dropdown-item small text-muted text-center" href="{{ url('admin/sessions') }}">
                                    <i class="bx bx-cog me-1"></i> Manage Sessions
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Date / Time Pill -->
                    <?php $date = date('d M l'); ?>
                    <div class="date-time-pill d-none d-lg-flex">
                        <span class="pill-date">📅 <?= $date ?></span>
                        <div class="pill-divider"></div>
                        <span id="live-time">--:--:--</span>
                        <button class="btn-holiday" onclick="showHolidays()">Holidays</button>
                    </div>

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

{{-- Global Search & Command Palette Modal --}}
<div class="modal fade" id="globalSearchModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg border-0" style="border-radius: 16px; overflow: hidden; background: #ffffff;">
            <div class="p-3 border-bottom d-flex align-items-center" style="background: #f8fafc;">
                <i class="bx bx-search text-primary me-2" style="font-size: 1.45rem;"></i>
                <input type="text" id="globalSearchInput" class="form-control form-control-lg border-0 shadow-none px-1" placeholder="Search by Roll No, Student Name, or Page... (e.g. 101, Aarti, Fee, CLC)" style="background: transparent; font-size: 1.05rem;" autocomplete="off">
                <span class="cmd-kbd-badge ms-2" onclick="closeGlobalSearch()" style="cursor:pointer;" title="Close">Esc</span>
            </div>
            <div class="modal-body p-3" style="max-height: 60vh; overflow-y: auto;" id="globalSearchResults">
                <!-- Quick Navigation Shortcuts (Default view) -->
                <div id="cmdDefaultNavigation">
                    <div class="d-flex align-items-center justify-content-between px-2 mb-2">
                        <span class="small fw-bold text-uppercase text-muted">⚡ Quick Page Shortcuts</span>
                        <span class="small text-muted">Press key combination anywhere</span>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <a href="{{ url('admin/students/create') }}" class="cmd-nav-item">
                                <span class="cmd-icon-box" style="background: rgba(22,163,74,0.12); color: #16a34a;"><i class="fa fa-user-plus"></i></span>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold">New Admission</div>
                                    <small class="text-muted">Create new student</small>
                                </div>
                                <kbd class="cmd-kbd-badge">Alt + N</kbd>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ url('admin/fee/management') }}" class="cmd-nav-item">
                                <span class="cmd-icon-box" style="background: rgba(201,168,76,0.15); color: #8b6914;"><i class="fa fa-file-invoice-dollar"></i></span>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold">Fee Management</div>
                                    <small class="text-muted">Collect fees & receipts</small>
                                </div>
                                <kbd class="cmd-kbd-badge">Alt + F</kbd>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ url('admin/students') }}" class="cmd-nav-item">
                                <span class="cmd-icon-box" style="background: rgba(37,99,235,0.1); color: #2563eb;"><i class="fa fa-users"></i></span>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold">Students Directory</div>
                                    <small class="text-muted">All students & certificates</small>
                                </div>
                                <kbd class="cmd-kbd-badge">Alt + S</kbd>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ url('home') }}" class="cmd-nav-item">
                                <span class="cmd-icon-box" style="background: rgba(14,165,233,0.12); color: #0284c7;"><i class="fa fa-home"></i></span>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold">Dashboard</div>
                                    <small class="text-muted">Overview & statistics</small>
                                </div>
                                <kbd class="cmd-kbd-badge">Alt + D</kbd>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ url('admin/students/strength') }}" class="cmd-nav-item">
                                <span class="cmd-icon-box" style="background: rgba(147,51,234,0.1); color: #7e22ce;"><i class="fa fa-chart-pie"></i></span>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold">Student Strength</div>
                                    <small class="text-muted">Course enrolment count</small>
                                </div>
                                <kbd class="cmd-kbd-badge">Alt + C</kbd>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ url('admin/accounts/income') }}" class="cmd-nav-item">
                                <span class="cmd-icon-box" style="background: rgba(220,38,38,0.1); color: #dc2626;"><i class="fa fa-chart-line"></i></span>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold">Accounts & Incomes</div>
                                    <small class="text-muted">Daily accounts ledger</small>
                                </div>
                                <kbd class="cmd-kbd-badge">Alt + A</kbd>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('admin.staff.attendance.markAttendancePre') }}" class="cmd-nav-item">
                                <span class="cmd-icon-box" style="background: rgba(13,148,136,0.12); color: #0d9488;"><i class="fa fa-calendar-check"></i></span>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold">Staff Attendance</div>
                                    <small class="text-muted">Mark daily staff records</small>
                                </div>
                                <kbd class="cmd-kbd-badge">Alt + M</kbd>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ url('admin/sessions') }}" class="cmd-nav-item">
                                <span class="cmd-icon-box" style="background: rgba(11,31,58,0.1); color: #0b1f3a;"><i class="fa fa-clock"></i></span>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold">Academic Sessions</div>
                                    <small class="text-muted">Manage academic years</small>
                                </div>
                                <kbd class="cmd-kbd-badge">Alt + Y</kbd>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Live Dynamic Search Results Container -->
                <div id="cmdDynamicResults" style="display: none;"></div>
            </div>
            <div class="p-2 px-3 border-top d-flex align-items-center justify-content-between small text-muted" style="background: #f8fafc;">
                <div><kbd class="cmd-kbd-badge me-1">Ctrl + K</kbd> Search &nbsp;·&nbsp; <kbd class="cmd-kbd-badge me-1">Esc</kbd> Close</div>
                <div><a href="javascript:;" onclick="closeGlobalSearch(); openShortcutsModal();" class="text-primary text-decoration-none fw-semibold"><i class="fa fa-keyboard me-1"></i>View All Shortcuts (<kbd class="cmd-kbd-badge">?</kbd>)</a></div>
            </div>
        </div>
    </div>
</div>

{{-- All Keyboard Shortcuts Guide Modal --}}
<div class="modal fade" id="shortcutsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow border-0" style="border-radius: 16px;">
            <div class="modal-header border-0 pb-1">
                <div>
                    <h5 class="modal-title fw-bold"><i class="fa fa-keyboard text-primary me-2"></i>Keyboard Shortcuts</h5>
                    <p class="text-muted small mb-0">Use these shortcuts from any page in the software.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-2">
                <div class="table-responsive">
                    <table class="table table-sm table-borderless align-middle mb-0">
                        <thead>
                            <tr class="text-uppercase text-muted border-bottom" style="font-size: 0.72rem;">
                                <th style="width: 35%;">Shortcut Key</th>
                                <th>Action / Destination</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><kbd class="cmd-kbd-badge">Ctrl + K</kbd> / <kbd class="cmd-kbd-badge">⌘K</kbd></td>
                                <td><strong>Global Search</strong> (Student, Roll No, Page)</td>
                            </tr>
                            <tr>
                                <td><kbd class="cmd-kbd-badge">Alt + N</kbd></td>
                                <td><strong>Add New Student</strong> Admission</td>
                            </tr>
                            <tr>
                                <td><kbd class="cmd-kbd-badge">Alt + S</kbd></td>
                                <td><strong>Students Directory</strong> (All Batches)</td>
                            </tr>
                            <tr>
                                <td><kbd class="cmd-kbd-badge">Alt + F</kbd></td>
                                <td><strong>Fee Management</strong> &amp; Collection</td>
                            </tr>
                            <tr>
                                <td><kbd class="cmd-kbd-badge">Alt + D</kbd></td>
                                <td><strong>Home Dashboard</strong></td>
                            </tr>
                            <tr>
                                <td><kbd class="cmd-kbd-badge">Alt + C</kbd></td>
                                <td><strong>Student Strength</strong></td>
                            </tr>
                            <tr>
                                <td><kbd class="cmd-kbd-badge">Alt + A</kbd></td>
                                <td><strong>Accounts &amp; Incomes</strong></td>
                            </tr>
                            <tr>
                                <td><kbd class="cmd-kbd-badge">Alt + M</kbd></td>
                                <td><strong>Mark Staff Attendance</strong></td>
                            </tr>
                            <tr>
                                <td><kbd class="cmd-kbd-badge">Alt + Y</kbd></td>
                                <td><strong>Academic Sessions</strong></td>
                            </tr>
                            <tr>
                                <td><kbd class="cmd-kbd-badge">Alt + P</kbd></td>
                                <td><strong>Print</strong> current page / form / receipt</td>
                            </tr>
                            <tr>
                                <td><kbd class="cmd-kbd-badge">?</kbd></td>
                                <td>Open this <strong>Shortcuts Guide</strong></td>
                            </tr>
                            <tr>
                                <td><kbd class="cmd-kbd-badge">Esc</kbd></td>
                                <td>Close any search, dialog, or modal</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-primary btn-sm px-4" data-bs-dismiss="modal">Got it!</button>
            </div>
        </div>
    </div>
</div>

{{-- Floating Speed Dial (Bottom Right) --}}
<div class="floating-speed-dial">
    <div class="speed-dial-menu" id="speedDialMenu">
        <a href="{{ url('admin/students/create') }}" class="speed-dial-item">
            <span>Add Student <kbd class="cmd-kbd-badge">Alt+N</kbd></span>
            <i class="fa fa-user-plus" style="background:#16a34a;"></i>
        </a>
        <a href="{{ url('admin/fee/management') }}" class="speed-dial-item">
            <span>Fee Collect <kbd class="cmd-kbd-badge">Alt+F</kbd></span>
            <i class="fa fa-file-invoice-dollar" style="background:#c9a84c;"></i>
        </a>
        <a href="javascript:;" onclick="openGlobalSearch()" class="speed-dial-item">
            <span>Quick Search <kbd class="cmd-kbd-badge">Ctrl+K</kbd></span>
            <i class="fa fa-search" style="background:#2563eb;"></i>
        </a>
        <a href="javascript:;" onclick="openShortcutsModal()" class="speed-dial-item">
            <span>Shortcuts <kbd class="cmd-kbd-badge">?</kbd></span>
            <i class="fa fa-keyboard" style="background:#7c3aed;"></i>
        </a>
    </div>
    <button type="button" class="speed-dial-main" id="speedDialToggle" title="Quick Actions (Alt + Q)">
        <i class="fa fa-bolt" id="speedDialIcon"></i>
    </button>
</div>

{{-- Global Shortcut Event Handlers --}}
<script>
var globalSearchModalInstance = null;
var shortcutsModalInstance = null;

function openGlobalSearch() {
    var modalEl = document.getElementById('globalSearchModal');
    if (!modalEl) return;
    if (!globalSearchModalInstance) {
        globalSearchModalInstance = new bootstrap.Modal(modalEl);
    }
    globalSearchModalInstance.show();
    setTimeout(function() {
        var input = document.getElementById('globalSearchInput');
        if (input) { input.focus(); input.select(); }
    }, 300);
}

function closeGlobalSearch() {
    if (globalSearchModalInstance) {
        globalSearchModalInstance.hide();
    }
}

function openShortcutsModal() {
    var modalEl = document.getElementById('shortcutsModal');
    if (!modalEl) return;
    if (!shortcutsModalInstance) {
        shortcutsModalInstance = new bootstrap.Modal(modalEl);
    }
    shortcutsModalInstance.show();
}

// Global Keyboard Hotkeys Listener
document.addEventListener('keydown', function(e) {
    var activeTag = document.activeElement ? document.activeElement.tagName : '';
    var isEditing = ['INPUT', 'TEXTAREA', 'SELECT'].includes(activeTag);

    // Ctrl + K or Cmd + K (Works everywhere)
    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
        e.preventDefault();
        openGlobalSearch();
        return;
    }

    // ? key opens shortcuts guide (only when NOT typing in an input)
    if (!isEditing && (e.key === '?' || (e.shiftKey && e.key === '/'))) {
        e.preventDefault();
        openShortcutsModal();
        return;
    }

    // Alt + Hotkeys
    if (e.altKey && !e.ctrlKey && !e.metaKey) {
        var key = e.key.toLowerCase();
        if (key === 'n') { e.preventDefault(); window.location.href = "{{ url('admin/students/create') }}"; }
        else if (key === 's') { e.preventDefault(); window.location.href = "{{ url('admin/students') }}"; }
        else if (key === 'f') { e.preventDefault(); window.location.href = "{{ url('admin/fee/management') }}"; }
        else if (key === 'd') { e.preventDefault(); window.location.href = "{{ url('home') }}"; }
        else if (key === 'c') { e.preventDefault(); window.location.href = "{{ url('admin/students/strength') }}"; }
        else if (key === 'a') { e.preventDefault(); window.location.href = "{{ url('admin/accounts/income') }}"; }
        else if (key === 'm') { e.preventDefault(); window.location.href = "{{ route('admin.staff.attendance.markAttendancePre') }}"; }
        else if (key === 'y') { e.preventDefault(); window.location.href = "{{ url('admin/sessions') }}"; }
        else if (key === 'p') { e.preventDefault(); window.print(); }
        else if (key === 'q') {
            e.preventDefault();
            var menu = document.getElementById('speedDialMenu');
            if (menu) menu.classList.toggle('show');
        }
    }
});

// Floating Speed Dial Toggle
document.addEventListener('DOMContentLoaded', function() {
    var dialBtn = document.getElementById('speedDialToggle');
    var dialMenu = document.getElementById('speedDialMenu');
    var dialIcon = document.getElementById('speedDialIcon');

    if (dialBtn && dialMenu) {
        dialBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            var isOpen = dialMenu.classList.toggle('show');
            if (dialIcon) {
                dialIcon.className = isOpen ? 'fa fa-times' : 'fa fa-bolt';
            }
        });

        document.addEventListener('click', function(e) {
            if (!dialBtn.contains(e.target) && !dialMenu.contains(e.target)) {
                dialMenu.classList.remove('show');
                if (dialIcon) dialIcon.className = 'fa fa-bolt';
            }
        });
    }

    // Live Instant Search in Command Palette
    var searchInput = document.getElementById('globalSearchInput');
    var defaultNav = document.getElementById('cmdDefaultNavigation');
    var dynamicResults = document.getElementById('cmdDynamicResults');
    var searchTimer = null;

    if (searchInput && dynamicResults && defaultNav) {
        searchInput.addEventListener('input', function() {
            var q = this.value.trim();
            clearTimeout(searchTimer);

            if (q.length === 0) {
                defaultNav.style.display = 'block';
                dynamicResults.style.display = 'none';
                dynamicResults.innerHTML = '';
                return;
            }

            searchTimer = setTimeout(function() {
                dynamicResults.innerHTML = '<div class="text-center py-4 text-muted"><i class="fa fa-spinner fa-spin me-2"></i>Searching student records...</div>';
                defaultNav.style.display = 'none';
                dynamicResults.style.display = 'block';

                fetch("{{ url('admin/students/quick-search') }}?q=" + encodeURIComponent(q))
                    .then(function(res) { return res.json(); })
                    .then(function(data) {
                        if (!data || data.length === 0) {
                            dynamicResults.innerHTML = '<div class="text-center py-4 text-muted"><i class="bx bx-info-circle me-1"></i>No student found for "<strong>' + $('<div>').text(q).html() + '</strong>".<br><a href="{{ url("admin/students/create") }}" class="btn btn-sm btn-outline-primary mt-2"><i class="fa fa-user-plus me-1"></i>Add New Student</a></div>';
                            return;
                        }

                        var html = '<div class="small fw-bold text-uppercase text-muted px-2 mb-2">🎓 Student Results (' + data.length + ')</div><div class="d-flex flex-column gap-2">';
                        data.forEach(function(s) {
                            html += '<div class="p-2 px-3 border rounded-3 bg-light d-flex align-items-center justify-content-between flex-wrap gap-2">' +
                                '<div>' +
                                    '<span class="badge bg-primary me-2">Roll #' + (s.roll_no || 'N/A') + '</span>' +
                                    '<strong>' + $('<div>').text(s.name).html() + '</strong>' +
                                    '<span class="text-muted ms-2 small">(' + (s.course || '—') + ' · ' + (s.batch || '') + ')</span>' +
                                '</div>' +
                                '<div class="d-flex align-items-center gap-1">' +
                                    '<a href="' + s.url_view + '" class="btn btn-sm btn-outline-dark px-2" title="View Admission Form"><i class="bx bx-show me-1"></i>Form</a>' +
                                    '<a href="' + s.url_fee + '" class="btn btn-sm btn-outline-success px-2" title="Collect Fees"><i class="fa fa-money-bill me-1"></i>Fee</a>' +
                                    '<a href="' + s.url_clc + '" class="btn btn-sm btn-outline-primary px-2" title="Issue CLC Certificate">CLC</a>' +
                                    '<a href="' + s.url_bonafide + '" class="btn btn-sm btn-outline-info px-2" title="Issue Bonafide Certificate">Bonafide</a>' +
                                '</div>' +
                            '</div>';
                        });
                        html += '</div>';
                        dynamicResults.innerHTML = html;
                    })
                    .catch(function(err) {
                        dynamicResults.innerHTML = '<div class="text-danger py-2 text-center">Search error. Please try again.</div>';
                    });
            }, 220);
        });
    }
});
</script>

@yield('scripts')
</body>
</html>
