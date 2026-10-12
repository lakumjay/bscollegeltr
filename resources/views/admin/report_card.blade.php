@extends('layouts.appadmin')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fa fa-file-alt me-2"></i>Report Card Preview</h4>
                    <a href="{{ route('exam_management.report_card_print', ['student_id' => $student->id, 'exam_id' => $exam->id]) }}" class="btn btn-success btn-sm" target="_blank"><i class="fa fa-print me-2"></i>Print</a>
                </div>
                <div class="card-body bg-light">
                    <h5>Student: {{ $student->first_name }} {{ $student->last_name }}</h5>
                    <p>Roll No: {{ $student->roll_no }}</p>
                    <p>Course: {{ $exam->course->name }}</p>
                    <p>Exam: {{ $exam->name }}</p>
                    <p>Session: {{ $exam->session->name ?? 'Current' }}</p>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Subject</th>
                                    <th>Marks</th>
                                    <th>Max Marks</th>
                                    <th>Grade</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($marks as $mark)
                                    <tr>
                                        <td>{{ $mark->subject->name }}</td>
                                        <td>{{ number_format($mark->marks, 2) }}</td>
                                        <td>{{ $exam->max_marks }}</td>
                                        <td>{{ $mark->grade }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <h5 class="mt-4">Summary</h5>
                    <p>Total Marks: {{ number_format($totalMarks, 2) }} / {{ $maxTotalMarks }}</p>
                    <p>Percentage: {{ number_format($percentage, 2) }}%</p>
                    <p>Grade: {{ $grade ?? '-' }}</p>
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
</style>
@endsection