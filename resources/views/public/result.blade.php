<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Result</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f4;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .card-header {
            border-radius: 10px 10px 0 0;
        }
        .form-control {
            border-radius: 6px;
        }
        .btn {
            border-radius: 6px;
            transition: all 0.2s ease;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .table th, .table td {
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fa fa-file-alt me-2"></i>View Exam Result</h4>
            </div>
            <div class="card-body">
                <form method="get" action="{{ route('exam_management.result_public') }}">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="student_id" class="form-label">Student ID / Roll No *</label>
                            <input type="text" class="form-control" id="student_id" name="student_id" required value="{{ request('student_id') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="exam_id" class="form-label">Select Exam *</label>
                            <select class="form-control" id="exam_id" name="exam_id" required>
                                <option value="">Select an Exam</option>
                                @foreach($exams as $exam)
                                    <option value="{{ $exam->id }}" {{ request('exam_id') == $exam->id ? 'selected' : '' }}>{{ $exam->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100"><i class="fa fa-search me-2"></i>View Result</button>
                        </div>
                    </div>
                </form>

                @if($student && $exam)
                    <div class="mt-4">
                        <h5>Student: {{ $student->first_name }} {{ $student->last_name }}</h5>
                        <p>Roll No: {{ $student->roll_no }}</p>
                        <p>Exam: {{ $exam->name }}</p>
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
                        <h5>Summary</h5>
                        <p>Total Marks: {{ number_format($totalMarks, 2) }} / {{ $maxTotalMarks }}</p>
                        <p>Percentage: {{ number_format($percentage, 2) }}%</p>
                        <p>Grade: {{ $grade ?? '-' }}</p>
                    </div>
                @elseif($request->has('student_id') && $request->has('exam_id'))
                    <div class="alert alert-danger mt-4">No result found for the given Student ID/Roll No and Exam.</div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>