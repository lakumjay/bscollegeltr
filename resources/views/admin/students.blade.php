@extends('layouts.appadmin')

@section('content')
<div class="container-fluid premium-page student-listing-page">
    <div class="premium-page-header">
        <div><span class="premium-page-eyebrow">Student directory</span><h1 class="premium-page-title">Students for {{ $course_name }}</h1><p class="premium-page-subtitle">Review admissions, update student records and generate documents.</p></div>
        <div class="premium-header-actions"><a href="{{ url('admin/students/strength') }}" class="premium-btn premium-btn-outline"><i class="bx bx-arrow-back"></i> Student strength</a><a href="{{ url('admin/students/create') }}" class="premium-btn premium-btn-gold"><i class="bx bx-user-plus"></i> Create student</a></div>
    </div>

    <div class="premium-card mb-4">
        <div class="premium-card-header"><div><h2><i class="bx bx-filter-alt me-2"></i>Search &amp; filter students</h2><p>Use one set of filters for the student list and downloads.</p></div></div>
        <div class="premium-card-body">
            <form id="studentFilterForm" method="get" action="{{ url('admin/students') }}" class="premium-form-grid student-unified-filter-form">
                <div class="premium-field"><label class="premium-label" for="student-search">Search students</label><input type="search" class="form-control" id="student-search" name="search" placeholder="Name or roll number" value="{{ request('search') }}"></div>
                <div class="premium-field"><label class="premium-label" for="student-course">Course</label><select id="student-course" name="course" class="form-select">
                                        <option value="">Select course</option>
                                        @foreach($allCourses as $c)
                                            <option value="{{ $c->id }}" {{ request('course') == $c->id ? 'selected' : '' }}>
                                                {{ $c->name }}
                                            </option>
                                        @endforeach
                                    </select></div>
                <div class="premium-field"><label class="premium-label" for="student-batch">Batch</label><select id="student-batch" name="batch" class="form-select">
                                        <option value="">-- All Batches --</option>
                                        @foreach($allBatches as $b)
                                            <option value="{{ $b }}" {{ request('batch') == $b ? 'selected' : '' }}>{{ $b }}</option>
                                        @endforeach
                                    </select></div>
                <div class="premium-field"><label class="premium-label" for="student-semester">Current semester</label><select id="student-semester" name="semester" class="form-select">
                                        <option value="">-- All Semesters --</option>
                                        @foreach(range(1, 8) as $semester)
                                            <option value="{{ $semester }}" {{ (int) request('semester') === $semester ? 'selected' : '' }}>Semester {{ $semester }}</option>
                                        @endforeach
                                    </select></div>
                <div class="premium-field"><label class="premium-label" for="student-subject">Subject</label><select id="student-subject" name="subject_id" class="form-select">
                                        <option value="">-- All Subjects --</option>
                                        @foreach($allSubjects as $sub)
                                            <option value="{{ $sub->id }}" {{ request('subject_id') == $sub->id ? 'selected' : '' }}>{{ $sub->name }}</option>
                                        @endforeach
                                    </select></div>
                <div class="student-filter-actions"><button type="submit" class="premium-btn premium-btn-navy"><i class="bx bx-search"></i> Apply filters</button><button type="button" onclick="doExport('excel')" class="premium-btn premium-btn-success"><i class="bx bx-download"></i> Download CSV</button><a href="{{ url('admin/students') }}?course={{ $course }}" class="premium-btn premium-btn-outline"><i class="bx bx-reset"></i> Clear</a></div>
            </form>
        </div>
    </div>

    <div class="premium-card"><div class="premium-card-header"><div><h2>Student records</h2><p>{{ $students->total() }} student{{ $students->total() === 1 ? '' : 's' }} found{{ request('search') ? ' for the current search' : '' }}.</p></div></div><div class="premium-card-body"><div class="table-responsive">
                    <table class="table premium-table" id="example22">
                        <thead>
                            <tr>
                                <th>Roll No</th>
                                <th>Name</th>
                                <th>Course</th>
                                <th>Batch</th>
                                <th>Current semester</th>
                                <th>Session</th>
                                <th style="display:none">Email Id</th>
                                <th style="display:none">Mobile</th>
                                <th style="display:none">Social Category</th>
                                <th>Status</th>
                                <th data-orderable="false">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                            @php
                                $current_sessions = $student->StudentCourse;
                                $currentSemester = optional($current_sessions->first())->semaster ?? '—';
                                $currentSessionName = optional(optional($current_sessions->first())->session)->session_name ?? '—';
                            @endphp
                            <tr>
                                <td>{{ $student->roll_no }}</td>
                                <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                <td>{{ optional($student->course_details)->name ?? '—' }}</td>
                                <td>{{ $student->batch }}</td>
                                <td>{{ $currentSemester }}</td>
                                <td>{{ $currentSessionName }}</td>
                                <td style="display:none">{{ $student->email }}</td>
                                <td style="display:none">{{ $student->phone_number }}</td>
                                <td style="display:none">{{ $student->category }}</td>
                                <td><span class="premium-status premium-status-{{ strtolower($student->status) }}">{{ $student->status }}</span></td>
                                <td>
                                    <div class="dropup student-actions-dropdown">
                                        <button class="premium-btn premium-btn-outline premium-btn-sm student-action-trigger js-student-actions-toggle" type="button" aria-expanded="false" aria-controls="studentActionMenu{{ $student->id }}">
                                            <i class="bx bx-dots-horizontal-rounded"></i> Actions
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end" id="studentActionMenu{{ $student->id }}">
                                            <li>
                                                <a class="dropdown-item" href="{{ url('admin/students/'.$student->id) }}">View</a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ url('admin/students/'.$student->id.'/edit') }}">Edit</a>
                                            </li>
                                            <li>
                                                <form method="post" action="{{ url('admin/students/'.$student->id) }}" class="js-delete-student" data-student-name="{{ $student->first_name }} {{ $student->last_name }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">Delete</button>
                                                </form>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="javascript:" data-bs-toggle="modal" data-bs-target="#Stu{{ $student->id }}">Promote</a>
                                            </li>
                                            <div class="dropdown-divider">Documents Generated</div>
@foreach($certificate_fields as $K=>$v)
                                            <li>
                                                <a class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#{{$K}}Certificate{{$student->id}}">{{ucfirst($K)}}</a>
                                            </li>
 @endforeach
                                           
                                            <li>
                                                <a class="dropdown-item" href="{{ url('admin/students/'.$student->id.'') }}">Admission Form</a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            @if($students->isEmpty())
                                <tr>
                                    <td colspan="12" class="text-center">No students found{{ $course ? ' for this course' : '' }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    @if($students->hasPages())
                        <div class="student-pagination">
                            <p>Showing {{ $students->firstItem() }} to {{ $students->lastItem() }} of {{ $students->total() }} results</p>
                            {{ $students->onEachSide(1)->links() }}
                        </div>
                    @endif
    </div></div></div>

    {{-- Modals container outside table to avoid z-index and overflow clipping --}}
    @foreach($students as $student)
        @php
            $current_sessions = $student->StudentCourse;
        @endphp

        {{-- Promote Modal --}}
        <div class="modal fade premium-modal" id="Stu{{ $student->id }}" tabindex="-1" role="dialog" aria-labelledby="StuModalLabel{{ $student->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <div>
                            <span class="premium-eyebrow text-warning d-block" style="font-size:0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Student Promotion</span>
                            <h5 class="modal-title" id="StuModalLabel{{ $student->id }}">Promote &mdash; {{ $student->first_name }} {{ $student->last_name }}</h5>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        @php
                            $studentSemester = (int) (optional($current_sessions->first())->semaster ?? $student->current_semester ?? 1);
                            $nextSemester = min($studentSemester + 1, 8);
                        @endphp
                        <h6 class="fw-bold mb-2 d-flex align-items-center">
                            <i class="bx bx-history me-2 text-primary"></i> Academic History
                        </h6>
                        <div class="table-responsive mb-3 border rounded">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="padding: 10px 14px; font-size: 0.8rem; text-transform: uppercase;">Session</th>
                                        <th style="padding: 10px 14px; font-size: 0.8rem; text-transform: uppercase;">Semester</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($current_sessions as $current_session)
                                        <tr>
                                            <td style="padding: 10px 14px;">{{ $current_session->session->session_name ?? '—' }}</td>
                                            <td style="padding: 10px 14px;">Semester {{ $current_session->semaster }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <hr class="my-3">
                        <form method="post" action="{{ url('admin/students/add_session/'.$student->id) }}">
                            @csrf
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-secondary mb-1" for="session-{{ $student->id }}">Session</label>
                                    <select class="form-select" id="session-{{ $student->id }}" required name="session">
                                        @foreach($sessions as $session)
                                            <option value="{{ $session->id }}" {{ (int) $session->id === $activeSessionId ? 'selected' : '' }}>{{ $session->session_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-semibold text-secondary mb-1" for="semester-{{ $student->id }}">Promote to Semester</label>
                                    <select class="form-select" name="semaster" id="semester-{{ $student->id }}" required>
                                        @foreach(range(1, 8) as $semester)
                                            <option value="{{ $semester }}" {{ $semester === $nextSemester ? 'selected' : '' }}>Semester {{ $semester }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 text-end mt-4">
                                    <button type="submit" class="btn btn-primary px-4 py-2">
                                        <i class="bx bx-check me-1"></i> Submit Promotion
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer bg-light px-4 py-3">
                        <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Certificate Modals --}}
        @foreach($certificate_fields as $K=>$v)
        <div class="modal fade premium-modal" id="{{$K}}Certificate{{$student->id}}" tabindex="-1" role="dialog" aria-labelledby="{{$K}}ModalLabel{{$student->id}}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <div>
                            <span class="premium-eyebrow text-warning d-block" style="font-size:0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Certificate Management</span>
                            <h5 class="modal-title" id="{{$K}}ModalLabel{{$student->id}}">{{strtoupper($K)}} Certificate &mdash; {{ $student->first_name }} {{ $student->last_name }}</h5>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <h6 class="fw-bold mb-3 d-flex align-items-center">
                            <i class="bx bx-history me-2 text-primary"></i> Generated Certificates
                        </h6>
                        @php
                            $clcCertificates = $student->relationLoaded('certificates')
                                ? $student->certificates->where('type', $K)->sortByDesc('id')->values()
                                : App\Models\StudentCertificate::where('student_id', $student->id)->where('type', $K)->latest()->get();
                        @endphp
                        @if(count($clcCertificates)==0)
                            <div class="alert alert-light border text-center py-3 text-muted mb-3">
                                <i class="bx bx-file-blank fs-4 d-block mb-1"></i>
                                No {{strtoupper($K)}} Certificate Generated Yet
                            </div>
                        @else
                            <div class="table-responsive mb-3 border rounded shadow-sm">
                                <table class="table table-hover align-middle mb-0" style="width: 100%;">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="padding: 10px 14px; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">{{ $K === 'clc' ? 'Certificate No.' : 'SR' }}</th>
                                            <th style="padding: 10px 14px; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">Date Generated</th>
                                            <th style="padding: 10px 14px; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($clcCertificates as $clcCertificate)
                                            <tr>
                                                <td style="padding: 10px 14px;"><strong class="text-dark">{{ $K === 'clc' ? ($clcCertificate->certificate_number ?: $clcCertificate->sr) : $clcCertificate->sr }}</strong></td>
                                                <td style="padding: 10px 14px; color: #5a6a80;">{{ \Carbon\Carbon::parse($clcCertificate->created_at)->format('d-m-Y h:i A') }}</td>
                                                <td style="padding: 10px 14px;" class="text-center">
                                                    <a href="{{ url('admin/students/'.$student->id.'/'.$K) }}?certId={{$clcCertificate->id}}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary px-3 py-1">
                                                        <i class="bx bx-printer me-1"></i> Print
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        @if($K === 'clc' && count($clcCertificates) > 0)
                            <div class="alert alert-info d-flex align-items-center py-3 px-3 mt-3 mb-0 border-0 shadow-sm" style="background-color: #e8f4fd; color: #0d47a1; border-radius: 8px;">
                                <i class="bx bx-check-circle me-2 fs-4 flex-shrink-0"></i>
                                <div><strong>Notice:</strong> CLC Certificate has already been issued for this student (CLC can only be generated once). You can print or re-open the certificate from the table above.</div>
                            </div>
                        @else
                            <hr class="my-4">
                            <h6 class="fw-bold mb-3 d-flex align-items-center">
                                <i class="bx bx-plus-circle me-2 text-success"></i> Generate New {{ strtoupper($K) }} Certificate
                            </h6>
                            <form action="{{url('admin/certificate-create/'.$student->id.'/'.$K)}}" method="post">
                                @csrf
                                <div class="row g-3">
                                    @foreach($certificate_fields[$K] as $key=>$val)
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold text-secondary mb-1">{{$val}}</label>
                                            <input type="text" name="data[{{$key}}]" class="form-control" placeholder="Enter {{$val}}" required/>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-4 text-end">
                                    <button type="submit" class="btn btn-primary px-4 py-2 certificate-generate-btn">
                                        <i class="bx bx-check me-1"></i> Generate Certificate
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                    <div class="modal-footer bg-light px-4 py-3">
                        <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    @endforeach
@endsection

@section('scripts')
<script>
function doExport(type) {
    var form   = document.getElementById('studentFilterForm');
    var params = new URLSearchParams(new FormData(form));
    var base   = '{{ route("admin.students.export.excel") }}';
    window.location.href = base + '?' + params.toString();
}

document.querySelectorAll('.js-delete-student').forEach(function (form) {
    form.addEventListener('submit', function (event) {
        event.preventDefault();
        var studentName = form.dataset.studentName.trim() || 'this student';

        if (typeof Swal === 'undefined') {
            if (window.confirm('Delete ' + studentName + '? This cannot be undone.')) form.submit();
            return;
        }

        Swal.fire({
            title: 'Delete student?',
            text: '“' + studentName + '” and the linked record will be permanently removed.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Keep student',
            reverseButtons: true,
            focusCancel: true,
            customClass: {
                confirmButton: 'premium-swal-delete',
                cancelButton: 'premium-swal-cancel'
            }
        }).then(function (result) {
            if (result.isConfirmed) form.submit();
        });
    });
});

var activeStudentActionMenu = null;

function closeStudentActionMenu() {
    if (!activeStudentActionMenu) return;

    var menu = activeStudentActionMenu.menu;
    menu.classList.remove('show', 'student-actions-floating');
    menu.removeAttribute('style');
    activeStudentActionMenu.container.appendChild(menu);
    activeStudentActionMenu.button.setAttribute('aria-expanded', 'false');
    activeStudentActionMenu = null;
}

document.querySelectorAll('.js-student-actions-toggle').forEach(function (button) {
    var container = button.closest('.student-actions-dropdown');
    var menu = container.querySelector('.dropdown-menu');

    button.addEventListener('click', function (event) {
        event.preventDefault();
        event.stopPropagation();

        if (activeStudentActionMenu && activeStudentActionMenu.menu === menu) {
            closeStudentActionMenu();
            return;
        }

        closeStudentActionMenu();
        document.body.appendChild(menu);
        menu.classList.add('show', 'student-actions-floating');
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
        activeStudentActionMenu = { button: button, container: container, menu: menu };
    });
});

document.addEventListener('click', closeStudentActionMenu);
document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') closeStudentActionMenu();
});
</script>
@endsection
