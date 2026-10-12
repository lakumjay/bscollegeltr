@extends('layouts.appadmin')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fa fa-pen me-2"></i>Marks/Grades Entry</h4>
                </div>
                <div class="card-body bg-light">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="get" action="{{ route('exam_management.marks_entry') }}">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="course_id" class="form-label">Select Course *</label>
                                <select class="form-control" id="course_id" name="course_id" required onchange="this.form.submit()">
                                    <option value="">Select a Course</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ $selectedCourseId == $course->id ? 'selected' : '' }}>{{ $course->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if($selectedCourseId)
                                <div class="col-md-4">
                                    <label for="exam_id" class="form-label">Select Exam *</label>
                                    <select class="form-control" id="exam_id" name="exam_id" required onchange="this.form.submit()">
                                        <option value="">Select an Exam</option>
                                        @foreach($exams as $exam)
                                            <option value="{{ $exam->id }}" {{ $selectedExamId == $exam->id ? 'selected' : '' }}>{{ $exam->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        </div>
                    </form>

                    @if($selectedExamId && !$students->isEmpty())
                        <form method="post" action="{{ route('exam_management.save_marks') }}" class="mt-4">
                            @csrf
                            <input type="hidden" name="course_id" value="{{ $selectedCourseId }}">
                            <input type="hidden" name="exam_id" value="{{ $selectedExamId }}">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Roll No</th>
                                            <th>Name</th>
                                            @foreach($subjects as $subject)
                                                <th>{{ $subject->name }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($students as $student)
                                            <tr>
                                                <td>{{ $student->roll_no }}</td>
                                                <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                                @foreach($subjects as $subject)
                                                    <td>
                                                        <input type="number" step="0.01" class="form-control" name="marks[{{ $student->id }}][{{ $subject->id }}]" value="{{ $marks[$student->id . '_' . $subject->id]->marks ?? '' }}" max="{{ $exams->find($selectedExamId)->max_marks }}">
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-save me-2"></i>Save Marks</button>
                        </form>
                    @elseif($selectedCourseId && $students->isEmpty())
                        <div class="alert alert-warning mt-4">No students found for this course in the current session.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border-radius: 10px;
}
.card-header {
    border-radius: 10px 10px 0 0;
    padding: 1rem 1.5rem;
}
.table th, .table td {
    vertical-align: middle;
}
.btn {
    transition: all 0.2s ease;
}
.btn:hover {
    transform: translateY(-2px);
}
.alert {
    border-radius: 8px;
}
.form-control {
    border-radius: 6px;
}
</style>
@endsection