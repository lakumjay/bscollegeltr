<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bonafide Certificate</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            font-size: 16px;
            line-height: 1.6;
            background-color: #f5f5f5;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .certificates-container {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            width: 100vw;
            height: 100vh;
        }
        .certificate {
            background-color: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 48vw;
            height: 90vh;
            margin: 1vw;
            padding: 1.2vw;
            box-sizing: border-box;
            position: relative;
            display: flex;
            flex-direction: column;
        }
        .certificate h1 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .certificate p {
            margin: 10px 0;
        }
        .underline {
            border-bottom: 2px dotted black;
            display: inline-block;
            padding-inline:10px;
            text-align: center;
        }
        .long-underline {
              border-bottom: 2px dotted black;
            display: inline-block;
            padding-inline:10px;
            text-align: center;
        }
        .date-align {
            display: inline-block;
            min-width: 150px;
            text-align: right;
            vertical-align: top;
            position: absolute;
            right: 1.2vw;
        }
        .signature {
            display: flex;
            justify-content: flex-end;
            margin-top: 40px;
        }
        .signature-wrapper {
            display: flex;
            justify-content: space-between;
            
        }
        .hindi {
            font-family: 'Noto Serif Devanagari', serif;
        }
        .cut-line {
            width: 0;
            border-left: 2px dashed #444;
            height: 92vh;
            align-self: center;
        }
        @media print {
            body {
                background-color: white;
            }
            .certificates-container {
                width: 297mm;
                height: 210mm;
                box-shadow: none;
            }
            .certificate {
                box-shadow: none;
                margin: 0;
                padding: 10mm;
                width: 50%;
                height: 100%;
            }
            .cut-line {
                border-left: 2px dashed #444;
                height: 100%;
            }
        }
        table{
        font-size:14px
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+Devanagari&display=swap" rel="stylesheet">
</head>
<body onload="window.print()">
    <div class="certificates-container">

        {{-- LEFT CERTIFICATE --}}
        <div class="certificate">
            <center>
                <img src="{{ url($profile->banner) }}" style="width:100%">
            </center>

            <p class="hindi">
                पत्रांक- <span class="underline">BFC{{ $srRight = $student->id }}</span>
                <span class="date-align">दिनांक- {{ date('d M Y') }}</span>
            </p>

            <h1>BONAFIDE CERTIFICATE</h1>

            <p>
                This is to certify that Mr./Mrs.
                <span class="underline"  >
                    {{ $student->first_name }} {{ $student->last_name }}
                </span>
           
                D/O, S/O
                <span class="underline">{{ $student->father_name }}</span>
        
                is a student of
                <span class="underline">{{ $student->course_details->name }}</span>
                Semester Roll No.
                <span class="underline">{{ $student->roll_no }}</span>
                Subject
                <span class="underline"  >
                    @if($subjects->isEmpty())
                        None
                    @else
                        {{ $subjects->pluck('name')->implode(', ') }}
                    @endif
                </span>
                for the academic year
                <span class="underline">
                   {!! str_repeat('&nbsp;', 12) !!}
 to {{ $student->academic_year_end }} {!! str_repeat('&nbsp;', 12) !!}

                </span>
            </p>

            <p>He/She is a Bonafide Student of our college.</p>
<table border="1">
<tr>
<th>Sl No.</th><th>Particulars</th><th>1st Year<br>Sem I</th>
<th>2st Year<br>Sem III</th>
<th>3st Year<br>Sem V</th>
<th>4st Year<br>Sem VII</th>
</tr>
<tr><td>1</td><td>Admission Form</td><td>200</td><td>200</td><td>200</td><td>200</td></tr>
<tr><td>2</td><td>Registration Fee</td><td>1150</td><td>1150</td><td>1150</td><td>1150</td></tr>
<tr><td>3</td><td>Tuition Fee</td><td>2400</td><td>2400</td><td>2400</td><td>2400</td></tr>
<tr><td>4</td><td>Practical Fee (Geo/Psy.)</td><td>360</td><td>360</td><td>360</td><td>360</td></tr>
<tr><td>5</td><td>Examination Fee</td><td>600</td><td>600</td><td>600</td><td>600</td></tr>
<tr><td>6</td><td>University Exam Fee</td><td>1200</td><td>1200</td><td>1200</td><td>1200</td></tr>
<tr><td colspan=2>Total Amount (Non-Practical)</td><td>6050</td><td>5550</td><td>5550</td><td>6250</td></tr>
<tr><td colspan=2>Total Amount (Practical)</td><td>6410</td><td>5910</td><td>5910</td><td>6610</td></tr>
</table>
            <div class="signature-wrapper">
                <div class="signature">
                    <div>Principal</div>
                </div>
                <div class="signature">
                    <div>Head Assistant</div>
                </div>
            </div>
        </div>

        <!-- CUT LINE -->
        <div class="cut-line"></div>
 <div class="certificate">
            <center>
                <img src="{{ url($profile->banner) }}" style="width:100%">
            </center>

            <p class="hindi">
                पत्रांक- <span class="underline">BFC{{ $srRight = $student->id }}</span>
                <span class="date-align">दिनांक- {{ date('d M Y') }}</span>
            </p>

            <h1>BONAFIDE CERTIFICATE</h1>

            <p>
                This is to certify that Mr./Mrs.
                <span class="underline"  >
                    {{ $student->first_name }} {{ $student->last_name }}
                </span>
           
                D/O, S/O
                <span class="underline">{{ $student->father_name }}</span>
        
                is a student of
                <span class="underline">{{ $student->course_details->name }}</span>
                Semester Roll No.
                <span class="underline">{{ $student->roll_no }}</span>
                Subject
                <span class="underline"  >
                    @if($subjects->isEmpty())
                        None
                    @else
                        {{ $subjects->pluck('name')->implode(', ') }}
                    @endif
                </span>
                for the academic year
                <span class="underline">
                   {!! str_repeat('&nbsp;', 12) !!}
 to {{ $student->academic_year_end }} {!! str_repeat('&nbsp;', 12) !!}

                </span>
            </p>

            <p>He/She is a Bonafide Student of our college.</p>
<table border="1">
<tr>
<th>Sl No.</th><th>Particulars</th><th>1st Year<br>Sem I</th>
<th>2st Year<br>Sem III</th>
<th>3st Year<br>Sem V</th>
<th>4st Year<br>Sem VII</th>
</tr>
<tr><td>1</td><td>Admission Form</td><td>200</td><td>200</td><td>200</td><td>200</td></tr>
<tr><td>2</td><td>Registration Fee</td><td>1150</td><td>1150</td><td>1150</td><td>1150</td></tr>
<tr><td>3</td><td>Tuition Fee</td><td>2400</td><td>2400</td><td>2400</td><td>2400</td></tr>
<tr><td>4</td><td>Practical Fee (Geo/Psy.)</td><td>360</td><td>360</td><td>360</td><td>360</td></tr>
<tr><td>5</td><td>Examination Fee</td><td>600</td><td>600</td><td>600</td><td>600</td></tr>
<tr><td>6</td><td>University Exam Fee</td><td>1200</td><td>1200</td><td>1200</td><td>1200</td></tr>
<tr><td colspan=2>Total Amount (Non-Practical)</td><td>6050</td><td>5550</td><td>5550</td><td>6250</td></tr>
<tr><td colspan=2>Total Amount (Practical)</td><td>6410</td><td>5910</td><td>5910</td><td>6610</td></tr>
</table>
            <div class="signature-wrapper">
                <div class="signature">
                    <div>Principal</div>
                </div>
                <div class="signature">
                    <div>Head Assistant</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
