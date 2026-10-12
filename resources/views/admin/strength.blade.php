@extends('layouts.appadmin')

@section('content')
<div class="container-fluid premium-page">
    <div class="premium-page-header">
        <div>
            <span class="premium-page-eyebrow">Student management</span>
            <h1 class="premium-page-title">Student Strength</h1>
            <p class="premium-page-subtitle">Review course-wise student enrolment at a glance.</p>
        </div>
        <div class="premium-header-actions"><a href="{{ url('admin/students/create') }}" class="premium-btn premium-btn-gold"><i class="bx bx-user-plus"></i> Add student</a></div>
    </div>
    <div class="premium-card">
        <div class="premium-card-header"><div><h2>Course strength directory</h2><p>Student count by gender and academic course.</p></div></div>
        <div class="premium-card-body">
            <div class="table-responsive"><table class="table premium-table js-premium-datatable">
            <thead><tr><th>Course</th><th>Boys</th><th>Girls</th><th>Others</th><th>Total</th><th class="premium-action-cell" data-orderable="false">Action</th></tr></thead>
                <tbody>@foreach($strength as $course)<tr>
                    <td><span class="premium-name">{{ $course->course_name }}</span></td>
                    <td><span class="premium-number">{{ $course->boys }}</span></td><td><span class="premium-number">{{ $course->girls }}</span></td><td><span class="premium-number">{{ $course->others }}</span></td><td><span class="premium-number">{{ $course->total }}</span></td>
                    <td class="premium-action-cell"><a href="{{ url('admin/students?course='.urlencode($course->course)) }}" class="premium-btn premium-btn-outline"><i class="bx bx-show"></i> View students</a></td>
                </tr>@endforeach</tbody>
            </table></div>
        </div>
    </div>
</div>
@endsection
