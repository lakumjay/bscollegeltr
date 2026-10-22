<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Character Certificate</title>
    <style>
        @page { size: A4 portrait; margin: 0; }
        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; }
        body { background: #ececec; color: #111; font-family: "Times New Roman", Times, serif; }
        .certificate {
            position: relative;
            width: 190mm;
            min-height: 277mm;
            margin: 10mm auto;
            padding: 8mm 12mm 12mm;
            overflow: hidden;
            background: #fff;
            border: 4px double #111;
        }
        .certificate::before {
            position: absolute;
            inset: 4mm;
            border: 1px solid #555;
            content: "";
            pointer-events: none;
            z-index: 1;
        }
        .watermark {
            position: absolute;
            top: 52%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 115mm;
            height: 115mm;
            opacity: 0.08;
            pointer-events: none;
            z-index: 0;
            object-fit: contain;
        }
        .certificate-content { position: relative; z-index: 2; }
        .college-header {
            border-bottom: 2px solid #111;
            padding-bottom: 3.5mm;
            margin-bottom: 4mm;
        }
        .header-body-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 3mm;
        }
        .college-logo-box {
            flex: 0 0 32mm;
            height: 32mm;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .college-logo {
            width: 32mm;
            height: 32mm;
            max-height: 100px;
            max-width: 100px;
            padding: 1.5mm;
            border: 1.5px solid #222;
            border-radius: 50%;
            object-fit: contain;
            background: #fff;
            display: block;
        }
        .college-info {
            flex: 1 1 auto;
            text-align: center;
            padding: 0 2mm;
        }
        .college-name {
            margin: 0 0 1.5mm;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 24px;
            font-weight: 900;
            line-height: 1.15;
            letter-spacing: .5px;
            text-transform: uppercase;
            color: #111;
            text-align: center;
        }
        .college-address { 
            margin: 0; 
            font-size: 13.5px; 
            font-weight: 700; 
            letter-spacing: .3px;
            line-height: 1.25;
        }
        .college-affiliation { 
            margin: 1.5mm 0 0; 
            font-size: 12.5px; 
            font-weight: 600;
            font-style: italic; 
            color: #222;
        }
        .student-photo-box {
            flex: 0 0 25mm;
            height: 30mm;
            border: 1px solid #333;
            padding: 1mm;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .student-photo {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .document-heading {
            text-align: center;
            margin: 5mm 0 4mm;
        }
        .certificate-title {
            display: inline-block;
            margin: 0 auto;
            padding: 2.2mm 6mm;
            border: 2px solid #111;
            font-size: 16px;
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: .5px;
            text-transform: uppercase;
            white-space: nowrap;
        }
        .meta-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            margin: 3mm 0 6mm;
            font-size: 13.5px;
        }
        .meta-left { text-align: left; }
        .meta-right { text-align: right; }
        .sl-no { font-size: 14px; font-weight: 700; }
        .cert-date { font-weight: 700; font-size: 13.5px; }
        .certificate-body {
            margin-top: 4mm;
            font-size: 14.5px;
            line-height: 1.8;
        }
        .field-row {
            display: flex;
            align-items: baseline;
            width: 100%;
            margin: 3.5mm 0;
        }
        .field-label {
            font-weight: 600;
            color: #111;
            white-space: nowrap;
            padding-right: 2.5mm;
        }
        .inline-fill {
            flex: 1 1 auto;
            border-bottom: 1.5px dotted #111;
            padding: 0 2mm 0.5mm;
            font-weight: 700;
            line-height: 1.25;
            min-width: 0;
        }
        .inline-fill.fixed-medium {
            flex: 0 0 65mm;
        }
        .inline-fill.fixed-short {
            flex: 0 0 45mm;
        }
        .statement-paragraph {
            margin: 6mm 0 0;
            font-size: 14.5px;
            line-height: 1.8;
            text-align: justify;
            font-style: italic;
        }
        .blessing {
            margin: 8mm 0 0;
            font-size: 15px;
            font-weight: 700;
            text-align: center;
        }
        .signatures {
            margin-top: 20mm;
            width: 100%;
        }
        .signature-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            width: 100%;
        }
        .signature-col {
            width: 46mm;
            text-align: center;
        }
        .signature-line {
            border-top: 1.5px solid #111;
            width: 100%;
            margin-bottom: 2mm;
        }
        .signature-label {
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .signature-col.left .signature-label {
            text-transform: none;
            font-weight: 700;
            font-size: 14px;
        }
        @media print {
            html, body {
                margin: 0 !important;
                padding: 0 !important;
                background: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .certificate {
                width: 190mm !important;
                min-height: 277mm !important;
                margin: 10mm auto !important;
                padding: 8mm 12mm 12mm !important;
                border: 4px double #111 !important;
                box-shadow: none !important;
                page-break-inside: avoid !important;
                page-break-after: avoid !important;
            }
        }
    </style>
</head>
<body onload="window.print()">
@php
    $studentName = trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')) ?: '—';
    $collegeName = $profile?->college_name ?? 'VANANCHAL COLLEGE';
    $collegeAddress = $profile?->address ?? 'TANDWA - 825321, Distt. - CHATRA (JHARKHAND)';
    $affiliation = $profile?->affiliation ?: ($profile?->university_name ? 'Permanent Affiliated to ' . $profile->university_name : 'Permanent Affiliated to Nilamber-Pitamber University, Medininagar.');
    $logoAsset = $profile?->logo ?? $profile?->banner ?? null;
    $logoUrl = $logoAsset && preg_match('/^(?:data:|https?:\/\/)/i', $logoAsset)
        ? $logoAsset
        : ($logoAsset && file_exists(public_path($logoAsset)) ? url($logoAsset) : asset('images/default-college-logo.svg'));
    $bannerAsset = $profile?->banner ?? null;
    $bannerUrl = $bannerAsset && preg_match('/^(?:data:|https?:\/\/)/i', $bannerAsset)
        ? $bannerAsset
        : ($bannerAsset && file_exists(public_path($bannerAsset)) ? url($bannerAsset) : null);
    
    $studentPhoto = null;
    if (!empty($student->photo)) {
        if (str_starts_with($student->photo, 'http')) {
            $studentPhoto = $student->photo;
        } elseif (file_exists(public_path($student->photo))) {
            $studentPhoto = url($student->photo);
        } elseif (file_exists(public_path('uploads/' . basename($student->photo)))) {
            $studentPhoto = url('uploads/' . basename($student->photo));
        }
    }

    $certData = json_decode($StudentCertificate->data ?? '{}') ?: (object) [];
    $courseName = optional($student->course_details)->name ?: ($student->course ?: '—');
    $sessionName = $certData->session ?? optional($student->session)->session_name ?: ($student->batch ?? '—');
    $academicYear = $certData->academic_year ?? null;
    $certSemester = $certData->semester ?? null;
    if (empty($academicYear) && preg_match('/^(\d{4})/', (string)$sessionName, $m)) {
        $startY = (int)$m[1];
        $academicYear = $startY . '-' . ($startY + 1);
    }
    $issueDate = optional($StudentCertificate->created_at)->format('d-m-Y') ?? now()->format('d-m-Y');
    $rawPrefix = strtoupper(trim((string) ($profile?->lc_prefix ?? '')));
    $cleanPrefix = trim(preg_replace('/[^A-Z0-9_\-\/]/', '', $rawPrefix), '/');
    $cleanPrefix = preg_replace('#/+#', '/', $cleanPrefix);
    $certYear = optional($StudentCertificate->created_at)->format('Y') ?? now()->format('Y');
    $srFormatted = sprintf('%04d', (int) ($StudentCertificate->sr ?? 1));
    $computedFallback = ($cleanPrefix !== '' ? $cleanPrefix . '/CHR/' : 'CHR/') . $certYear . '/' . $srFormatted;
    $certificateNumber = $StudentCertificate->certificate_number ?: $computedFallback;
    $serialNumber = str_pad((string) ($StudentCertificate->sr ?? 1), 5, '0', STR_PAD_LEFT);
@endphp

<main class="certificate">
    @if($logoUrl)
        <img class="watermark" src="{{ $logoUrl }}" alt="Watermark">
    @endif

    <div class="certificate-content">
        @if($bannerUrl)
            <div class="college-banner-header" style="width: 100%; text-align: center; margin-bottom: 5mm;">
                <img src="{{ $bannerUrl }}" style="width: 100%; max-height: 32mm; object-fit: contain; display: block; margin: 0 auto;" alt="{{ $collegeName }}">
            </div>
        @else
            <header class="college-header">
                <div class="header-body-row">
                    @if($logoUrl)
                        <div class="college-logo-box">
                            <img class="college-logo" src="{{ $logoUrl }}" onerror="this.onerror=null;this.src='{{ asset('images/default-college-logo.svg') }}';" alt="{{ $collegeName }} emblem">
                        </div>
                    @else
                        <div style="flex: 0 0 32mm;"></div>
                    @endif
                    <div class="college-info">
                        <h1 class="college-name">{{ $collegeName }}</h1>
                        <p class="college-address">{{ $collegeAddress }}</p>
                        <p class="college-affiliation">{{ $affiliation }}</p>
                    </div>
                    @if($studentPhoto)
                        <div class="student-photo-box">
                            <img class="student-photo" src="{{ $studentPhoto }}" onerror="this.onerror=null;this.parentElement.style.display='none';" alt="{{ $studentName }}">
                        </div>
                    @else
                        <div style="flex: 0 0 32mm;"></div>
                    @endif
                </div>
            </header>
        @endif

        <section class="document-heading">
            <h2 class="certificate-title">CHARACTER CERTIFICATE</h2>
        </section>

        <section class="meta-row">
            <div class="meta-left">
                <div class="sl-no">Certificate No. &nbsp; <strong>{{ $certificateNumber }}</strong></div>
            </div>
            <div class="meta-right">
                <div class="cert-date">Date: <span style="border-bottom: 1.5px dotted #111; padding: 0 3mm 0.5mm; display: inline-block;"><strong>{{ $issueDate }}</strong></span></div>
            </div>
        </section>

        <section class="certificate-body">
            <div class="field-row">
                <span class="field-label">Name of Student</span>
                <span class="inline-fill"><strong>{{ $studentName }}</strong></span>
            </div>

            <div class="field-row">
                <span class="field-label">Son/Daughter of</span>
                <span class="inline-fill"><strong>{{ $student->father_name ?? '—' }}</strong></span>
            </div>

            <div class="field-row">
                <span class="field-label">Resident of</span>
                <span class="inline-fill"><strong>{{ $student->permanent_address ?? '—' }}</strong></span>
            </div>

            <div class="field-row">
                <span class="field-label">is/was a regular Student of this college for the class</span>
                <span class="inline-fill"><strong>{{ $courseName }}</strong></span>
            </div>

            @if(!empty($certSemester))
                <div class="field-row">
                    <span class="field-label">Semester</span>
                    <span class="inline-fill fixed-short"><strong>{{ $certSemester }}</strong></span>
                </div>
            @endif

            <div class="field-row">
                <span class="field-label">Session</span>
                <span class="inline-fill fixed-short"><strong>{{ $sessionName }}</strong></span>
                @if(!empty($academicYear))
                    <span class="field-label" style="padding-left: 6mm;">Academic Year</span>
                    <span class="inline-fill fixed-short"><strong>{{ $academicYear }}</strong></span>
                @endif
            </div>

            <p class="statement-paragraph">
                He/She bears a good moral character. His/Her conduct in this college is/was satisfactory. He/She is/was a polite and well behaved Student.
            </p>

            <p class="blessing">I wish him/her for Bright future.</p>
        </section>

        <footer class="signatures">
            <div class="signature-row">
                <div class="signature-col left">
                    <div class="signature-line"></div>
                    <div class="signature-label">Assistant</div>
                </div>
                <div class="signature-col right">
                    <div class="signature-line"></div>
                    <div class="signature-label">PRINCIPAL</div>
                </div>
            </div>
        </footer>
    </div>
</main>
</body>
</html>
