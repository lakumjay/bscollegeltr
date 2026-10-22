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
            font-size: 15px;
            line-height: 1.5;
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
            height: 94vh;
            margin: 0.5vw;
            padding: 1vw 1.2vw;
            box-sizing: border-box;
            position: relative;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80mm;
            height: 80mm;
            opacity: 0.08;
            pointer-events: none;
            z-index: 0;
            object-fit: contain;
        }
        .certificate-inner {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .college-banner-container {
            width: 100%;
            text-align: center;
            margin: 0 auto 6px;
        }
        .college-banner-img {
            width: 100%;
            max-width: 100%;
            max-height: 105px;
            height: auto;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }
        .certificate h1 {
            text-align: center;
            font-size: 21px;
            margin: 8px 0 12px;
            letter-spacing: 0.5px;
        }
        .certificate p {
            margin: 6px 0;
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
            margin-top: 25px;
        }
        .signature-wrapper {
            display: flex;
            margin-top: 15px;
            justify-content: space-between;
        }
        .hindi {
            font-family: 'Noto Serif Devanagari', serif;
        }
        .cut-line {
            width: 0;
            border-left: 2px dashed #444;
            height: 94vh;
            align-self: center;
        }
        .fee-structure-img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 4px auto;
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
                padding: 6mm 8mm;
                width: 50%;
                height: 100%;
                page-break-inside: avoid;
            }
            .cut-line {
                border-left: 2px dashed #444;
                height: 100%;
            }
            .college-banner-img {
                max-height: 95px !important;
            }
        }
        table{
            font-size:14px;
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+Devanagari&display=swap" rel="stylesheet">
</head>
<body onload="window.print()">
@php
    $logoAsset = $profile?->logo ?? $profile?->banner ?? null;
    $logoUrl = $logoAsset && preg_match('/^(?:data:|https?:\/\/)/i', $logoAsset)
        ? $logoAsset
        : ($logoAsset && file_exists(public_path($logoAsset)) ? url($logoAsset) : asset('images/default-college-logo.svg'));
    $bannerUrl = $profile?->banner && file_exists(public_path($profile->banner)) ? url($profile->banner) : $logoUrl;
@endphp
    <div class="certificates-container">

        {{-- LEFT CERTIFICATE (Student Copy) --}}
        <div class="certificate">
            @if($logoUrl)
                <img class="watermark" src="{{ $logoUrl }}" alt="Watermark">
            @endif
            <div class="certificate-inner">
                <div style="text-align:right;margin-bottom:4px;"><span style="background:#2c5aa0;color:#fff;font-size:9px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;border-radius:6px;padding:2px 8px;">Student Copy</span></div>
                <div class="college-banner-container">
                    <img src="{{ $bannerUrl }}" class="college-banner-img" alt="College Banner">
                </div>

            <p class="hindi">
                पत्रांक- <font style="font-weight:600;  font-size: 15px;">{{ $StudentCertificate->certificate_number ?: (($profile->lc_prefix ?? 'BSM/LTR') . '-' . $StudentCertificate->sr) }}</font>
                <span class="date-align">दिनांक- {{ date('d M Y') }}</span>
            </p>

            <h1>BONAFIDE CERTIFICATE</h1>

            <p style="    text-align: justify;">
                This is to certify that Mr./Mrs.
                <span class="underline"  >
                    {{ $student->first_name }} {{ $student->last_name }}
                </span>
           
                D/O, S/O
                <span class="underline">{{ $student->father_name }}</span>
        
                is a student of
                <span class="underline">{{ $student->course_details->name }}</span>
                Semester<span class="underline">
                   {!! str_repeat('&nbsp;', 12) !!} </span> Roll No.
                <span class="underline">{{ $student->roll_no }}</span>
                Subject
                <span class="underline"  >
                    @if($subjects->isEmpty())
                        None
                    @else
                        {{ $subjects->pluck('name')->implode(', ') }}
                    @endif
                </span>
                Session <span class="underline">
                   {!! str_repeat('&nbsp;', 12) !!} </span>
                for the academic year
                <span class="underline">
                   {!! str_repeat('&nbsp;', 12) !!}
 to {{ $student->academic_year_end }} {!! str_repeat('&nbsp;', 12) !!}

                </span>
            </p>
<p>He/She is a Bonafied Student of our college</p>
           <center><b>Fee Structure</b></center>
            <img src="{{url('fee_structure.png')}}" class="fee-structure-img" alt="Fee Structure"/>
            <div class="signature-wrapper">
                <div class="signature">
                    <div>Assistant</div>
                </div>
                <div class="signature">
                    <div> Principal </div>
                </div>
            </div>
            </div>
        </div>

        <!-- CUT LINE -->
        <div class="cut-line"></div>
        <div class="certificate">
            @if($logoUrl)
                <img class="watermark" src="{{ $logoUrl }}" alt="Watermark">
            @endif
            <div class="certificate-inner">
                <div style="text-align:right;margin-bottom:4px;"><span style="background:#333;color:#fff;font-size:9px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;border-radius:6px;padding:2px 8px;">College Copy</span></div>
                <div class="college-banner-container">
                    <img src="{{ $bannerUrl }}" class="college-banner-img" alt="College Banner">
                </div>

                <p class="hindi">
                    पत्रांक <font style="font-weight:600;  font-size: 15px;">{{ $StudentCertificate->certificate_number ?: (($profile->lc_prefix ?? 'BSM/LTR') . '-' . $StudentCertificate->sr) }}</font>
                    <span class="date-align">दिनांक- {{ date('d M Y') }}</span>
                </p>

                <h1>BONAFIDE CERTIFICATE</h1>

                <p style="    text-align: justify;">
                    This is to certify that Mr./Mrs.
                    <span class="underline"  >
                        {{ $student->first_name }} {{ $student->last_name }}
                    </span>
               
                    D/O, S/O
                    <span class="underline">{{ $student->father_name }}</span>
            
                    is a student of
                    <span class="underline">{{ $student->course_details->name }}</span>
                    Semester  <span class="underline">
                       {!! str_repeat('&nbsp;', 12) !!} </span>Roll No.
                    <span class="underline">{{ $student->roll_no }}</span>
                    Subject
                    <span class="underline"  >
                        @if($subjects->isEmpty())
                            None
                        @else
                            {{ $subjects->pluck('name')->implode(', ') }}
                        @endif
                    </span>
                    Session <span class="underline">
                       {!! str_repeat('&nbsp;', 12) !!} </span>
                    for the academic year
                    <span class="underline">
                       {!! str_repeat('&nbsp;', 12) !!}
     to {{ $student->academic_year_end }} {!! str_repeat('&nbsp;', 12) !!}

                    </span>
                </p>
    <p>He/She is a Bonafied Student of our college</p>
    <center><b>Fee Structure</b></center>
                <img src="{{url('fee_structure.png')}}" class="fee-structure-img" alt="Fee Structure"/>
                <div class="signature-wrapper">
                    <div class="signature">
                        <div>Assistant</div>
                    </div>
                    <div class="signature">
                        <div>
                         Principal</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
