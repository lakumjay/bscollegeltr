<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Card</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #007bff;
        }
        .student-info {
            margin-bottom: 20px;
        }
        .student-info p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: #fff;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .summary {
            margin-top: 20px;
        }
        .summary p {
            margin: 5px 0;
            font-weight: bold;
        }
        @media print {
            body {
                padding: 0;
                background: none;
            }
            .container {
                box-shadow: none;
                border: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">        <div class="header">

       <center> <img src="{{ url(Auth::user()->profile->banner) }}"></center>        </div>

        <div class="header">
            <h1>Report Card</h1>
        </div>
        <div class="student-info">
            <p><strong>Student:</strong> {{ $student->first_name }} {{ $student->last_name }}</p>
            <p><strong>Roll No:</strong> {{ $student->roll_no }}</p>
            <p><strong>Course:</strong> {{ $exam->course->name }}</p>
            <p><strong>Exam:</strong> {{ $exam->name }}</p>
            <p><strong>Session:</strong> {{ $exam->session->name ?? 'Current' }}</p>
        </div>
        <table>
            <thead>
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
        <div class="summary">
            <p>Total Marks: {{ number_format($totalMarks, 2) }} / {{ $maxTotalMarks }}</p>
            <p>Percentage: {{ number_format($percentage, 2) }}%</p>
            <p>Grade: {{ $grade ?? '-' }}</p>
        </div>
    </div>
</body>
</html>