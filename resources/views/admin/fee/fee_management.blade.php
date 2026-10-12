@extends('layouts.appadmin')

@section('content')
<div class="premium-page finance-page">
    <section class="premium-page-header">
        <div><span class="premium-eyebrow">Fee management</span><h1>Student fee directory</h1><p>Select a course, find a student, and open the complete fee record.</p></div>
    </section>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif
    @if($errors->any()) <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div> @endif

    <section class="premium-content-card finance-filter-card">
        <div class="premium-card-heading"><div><span class="premium-eyebrow">Course selection</span><h2>Choose a course</h2></div><i class="fa fa-graduation-cap"></i></div>
        <form method="GET" action="{{ route('fee_management.index') }}" class="finance-course-form">
            <div class="finance-field"><label for="course_id">Course <span>*</span></label><select id="course_id" name="course_id" required><option value="">Select a course</option>@foreach($courses as $course)<option value="{{ $course->id }}" {{ $selectedCourseId == $course->id ? 'selected' : '' }}>{{ $course->name }}</option>@endforeach</select></div>
            <button type="submit" class="premium-btn premium-btn-primary"><i class="fa fa-arrow-right"></i> View students</button>
        </form>
    </section>

    @if($selectedCourseId)
        <section class="premium-content-card finance-table-card">
            <div class="premium-card-heading"><div><span class="premium-eyebrow">Students</span><h2>Fee records</h2></div><span class="premium-count-badge">{{ $students->count() }} on this page</span></div>
            <form method="GET" action="{{ route('fee_management.index') }}" class="finance-search-form">
                <input type="hidden" name="course_id" value="{{ $selectedCourseId }}">
                <div class="finance-field"><label for="studentSearch">Search student</label><input id="studentSearch" type="text" name="search" placeholder="Name or roll number" value="{{ request('search') }}"></div>
                <div class="finance-field"><label for="semesterFilter">Semester</label><select id="semesterFilter" name="sem_filter"><option value="">All semesters</option>@for($semester = 1; $semester <= 8; $semester++)<option value="{{ $semester }}" {{ request('sem_filter') == $semester ? 'selected' : '' }}>Semester {{ $semester }}</option>@endfor</select></div>
                <button type="submit" class="premium-btn premium-btn-primary"><i class="fa fa-search"></i> Search</button>
            </form>

            @if($students->isEmpty())
                <div class="premium-empty-state"><i class="fa fa-users"></i><h3>No students found</h3><p>Try another search or semester filter.</p></div>
            @else
                <div class="premium-table-wrap"><table class="premium-table"><thead><tr><th>Roll no</th><th>Student</th><th>Current semester</th><th>Batch</th><th class="text-end">Action</th></tr></thead><tbody>
                @foreach($students as $student)
                    @php
                        $studentSession = $student->StudentCourse->firstWhere('session_id', Auth::user()->active_session_id);
                        $currentYear = $studentSession->semaster ?? '--';
                    @endphp
                    @if(!request()->filled('sem_filter') || $currentYear == request('sem_filter'))
                    <tr><td>{{ $student->roll_no ?: '-' }}</td><td><strong>{{ $student->first_name }} {{ $student->last_name }}</strong></td><td><span class="finance-semester-badge">{{ $currentYear }}</span></td><td>{{ $student->batch ?: '-' }}</td><td class="text-end">
                        @if(true || \App\Models\FeeRecord::where('student_id', $student->id)->where('course_id', $selectedCourseId)->where('session_id', Auth::user()->active_session_id)->exists())
                            <a href="{{ route('fee_management.view', ['student_id' => $student->id, 'course_id' => $selectedCourseId]) }}" class="premium-btn premium-btn-outline premium-btn-sm"><i class="fa fa-eye"></i> View fees</a>
                        @else
                            <button type="button" class="premium-btn premium-btn-primary premium-btn-sm" data-bs-toggle="modal" data-bs-target="#generateFeeModal{{ $student->id }}"><i class="fa fa-plus"></i> Generate fee</button>
                        @endif
                    </td></tr>

                    <div class="modal fade" id="generateFeeModal{{ $student->id }}" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content premium-modal"><div class="modal-header"><div><span class="premium-eyebrow">Fee generation</span><h5>Generate fee for {{ $student->first_name }}</h5></div><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><form method="POST" action="{{ route('fee_management.generate') }}">@csrf<div class="modal-body"><input type="hidden" name="student_id" value="{{ $student->id }}"><input type="hidden" name="course_id" value="{{ $selectedCourseId }}"><div class="finance-field"><label>Special category <small>(optional)</small></label><select name="special_category_id"><option value="">None</option>@foreach(\App\Models\SpecialCategory::where('course_id', $selectedCourseId)->where('user_id', Auth::id())->get() as $category)<option value="{{ $category->id }}">{{ $category->category_name }}</option>@endforeach</select></div></div><div class="modal-footer"><button type="button" class="premium-btn premium-btn-outline" data-bs-dismiss="modal">Cancel</button><button type="submit" class="premium-btn premium-btn-primary">Generate fee</button></div></form></div></div></div>
                    @endif
                @endforeach
                </tbody></table></div>
                <div class="premium-pagination">{{ $students->appends(request()->query())->links() }}</div>
            @endif
        </section>
    @endif
</div>
@endsection
