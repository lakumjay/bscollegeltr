<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>College Leaving / Transfer Certificate</title>
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
            padding: 8mm 10mm 10mm;
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
            display: table;
            width: 100%;
            margin: 4mm 0 4mm;
            table-layout: fixed;
        }
        .document-heading > div { display: table-cell; width: 30%; vertical-align: middle; }
        .document-heading .title-cell { width: 40%; text-align: center; }
        .serial-number { font-size: 12.5px; font-weight: 700; text-align: right; white-space: nowrap; }
        .certificate-number { font-size: 11.5px; text-align: left; }
        .certificate-title {
            display: inline-block;
            margin: 0;
            padding: 2mm 4.5mm;
            border: 2px solid #111;
            font-size: 15px;
            font-weight: 700;
            line-height: 1.1;
            white-space: nowrap;
        }
        .numbered-fields { margin-top: 1mm; font-size: 13.5px; line-height: 1.6; }
        .field-row {
            display: flex;
            align-items: baseline;
            width: 100%;
            margin: 3.2mm 0;
        }
        .field-number {
            flex: 0 0 6.5mm;
            font-weight: 700;
        }
        .field-label {
            white-space: nowrap;
            padding-right: 2.5mm;
            font-weight: 500;
        }
        .field-fill {
            flex: 1 1 auto;
            border-bottom: 1.5px dotted #111;
            padding: 0 2mm 0.5mm;
            min-width: 0;
            line-height: 1.25;
        }
        .field-fill.strong { font-weight: 700; }
        .field-fill.center { text-align: center; }

        .academic-complex-wrap {
            flex: 1 1 auto;
            width: calc(100% - 6.5mm);
        }
        .academic-subrow {
            display: flex;
            align-items: baseline;
            gap: 2mm;
            width: 100%;
            margin-bottom: 3.2mm;
            white-space: nowrap;
        }
        .academic-subrow:last-child { margin-bottom: 0; }
        .statement-row {
            display: flex;
            align-items: baseline;
            width: 100%;
            margin: 3.2mm 0;
        }
        .statement-text {
            flex: 1 1 auto;
            text-align: justify;
            line-height: 1.6;
        }
        .statement-fill {
            display: inline-block;
            border-bottom: 1.5px dotted #111;
            padding: 0 2mm 0.5mm;
            font-weight: 700;
            text-align: center;
        }
        .blessing { margin: 6mm 0 0; font-size: 14.5px; font-weight: 700; text-align: center; }
        .signatures {
            margin-top: 10mm;
            width: 100%;
        }
        .signature-date {
            font-size: 13.5px;
            font-weight: 700;
            margin-bottom: 9mm;
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
            font-size: 13.5px;
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
                padding: 8mm 10mm 10mm !important;
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
    $certificateData = json_decode($StudentCertificate->data ?? '{}') ?: (object) [];
    $studentName = trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')) ?: '—';
    $collegeName = $profile?->college_name ?? 'VANANCHAL COLLEGE';
    $collegeAddress = $profile?->address ?? 'TANDWA - 825321, Distt. - CHATRA (JHARKHAND)';
    $affiliation = $profile?->affiliation ?? 'Permanent Affiliated to Vinoba Bhave University, Hazaribag';
    $universityName = $profile?->university_name ?? 'Vinoba Bhave University, Hazaribag';
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

    $courseName = optional($student->course_details)->name ?: '—';
    $sessionName = $certificateData->session ?? optional($student->session)->session_name ?: ($student->batch ?? '—');
    $academicYear = $certificateData->academic_year ?? null;
    if (empty($academicYear) && preg_match('/^(\d{4})/', (string)$sessionName, $m)) {
        $startY = (int)$m[1];
        $academicYear = $startY . '-' . ($startY + 1);
    }
    $issueDate = optional($StudentCertificate->created_at)->format('d-m-Y') ?? now()->format('d-m-Y');
    $dateOfBirth = optional($student->date_of_birth)->format('d-m-Y') ?? '—';
    $registrationNumber = $certificateData->registration_number ?? $certificateData->registrtaion_number ?? $student->reg_no ?? '—';
    $examinationYear = $certificateData->examination_year ?? $student->current_year ?? '—';
    $conduct = $certificateData->conduct ?? 'Good';
    $characterRecord = $certificateData->character_record ?? 'recorded';
    $rawPrefix = strtoupper(trim((string) ($profile?->lc_prefix ?? '')));
    $cleanPrefix = trim(preg_replace('/[^A-Z0-9_\-\/]/', '', $rawPrefix), '/');
    $cleanPrefix = preg_replace('#/+#', '/', $cleanPrefix);
    $certYear = optional($StudentCertificate->created_at)->format('Y') ?? now()->format('Y');
    $srFormatted = sprintf('%04d', (int) ($StudentCertificate->sr ?? 1));
    $computedFallback = ($cleanPrefix !== '' ? $cleanPrefix . '/' : '') . $certYear . '/' . $srFormatted;
    $certificateNumber = $StudentCertificate->certificate_number ?: $computedFallback;
    $serialNumber = str_pad((string) ($StudentCertificate->sr ?? 1), 5, '0', STR_PAD_LEFT);
    $studentCategory = strtoupper(trim((string) ($student->category ?? '—'))) ?: '—';
    $subjectIds = is_array($student->subjects)
        ? $student->subjects
        : (json_decode($student->subjects ?? '[]', true) ?: []);
    $subjectNames = !empty($subjectIds)
        ? \App\Models\Subject::whereIn('id', $subjectIds)->pluck('name')->filter()->implode(', ')
        : '';
    $subjects = $subjectNames ?: (optional($student->course_details)->name ?: 'General');
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
            <div class="certificate-number">Certificate No.<br><strong>{{ $certificateNumber }}</strong></div>
            <div class="title-cell"><h2 class="certificate-title">College Leaving / Transfer certificate</h2></div>
            <div class="serial-number">Serial No.: {{ $serialNumber }}</div>
        </section>

        <section class="numbered-fields">
            <div class="field-row">
                <div class="field-number">1.</div>
                <div class="field-label">Name</div>
                <div class="field-fill strong">{{ $studentName }}</div>
            </div>

            <div class="field-row">
                <div class="field-number">2.</div>
                <div class="field-label">Father's Name</div>
                <div class="field-fill strong">{{ $student->father_name ?? '—' }}</div>
            </div>

            <div class="field-row">
                <div class="field-number">3.</div>
                <div class="field-label">Date of Birth</div>
                <div class="field-fill strong">{{ $dateOfBirth }}</div>
            </div>

            <div class="field-row">
                <div class="field-number">4.</div>
                <div class="field-label">Permanent Address</div>
                <div class="field-fill strong">{{ $student->permanent_address ?? '—' }}</div>
            </div>

            <div class="field-row">
                <div class="field-number">5.</div>
                <div class="academic-complex-wrap">
                    <div class="academic-subrow">
                        <span>Has Passed</span>
                        <div class="field-fill strong center">{{ $courseName }}</div>
                        <span>from</span>
                    </div>
                    <div class="academic-subrow">
                        <div class="field-fill strong">{{ $universityName }}</div>
                        <span>of Session</span>
                        <div class="field-fill strong center" style="flex: 0 0 30mm;">{{ $sessionName }}</div>
                        @if(!empty($academicYear))
                            <span>Academic Year</span>
                            <div class="field-fill strong center" style="flex: 0 0 28mm;">{{ $academicYear }}</div>
                        @endif
                    </div>
                    <div class="academic-subrow">
                        <span>Roll</span>
                        <div class="field-fill strong center" style="flex: 0 0 25mm;">{{ $student->roll_no ?? '—' }}</div>
                        <span>No.</span>
                        <div class="field-fill strong center" style="flex: 0 0 30mm;">{{ $student->student_id ?? $student->reg_no ?? '—' }}</div>
                        <span>Registration</span>
                    </div>
                    <div class="academic-subrow">
                        <span>No.</span>
                        <div class="field-fill strong center">{{ $registrationNumber }}</div>
                        <span>Year</span>
                        <div class="field-fill strong center" style="flex: 0 0 35mm;">{{ $examinationYear }}</div>
                    </div>
                </div>
            </div>

            <div class="field-row">
                <div class="field-number">6.</div>
                <div class="field-label">Category</div>
                <div class="field-fill strong">{{ $studentCategory }}</div>
            </div>

            <div class="field-row">
                <div class="field-number">7.</div>
                <div class="field-label">Subject</div>
                <div class="field-fill strong">{{ $subjects }}</div>
            </div>

            <div class="statement-row">
                <div class="field-number">8.</div>
                <div class="statement-text">
                    All sums due by him to the College have been paid, including fees up to <span class="statement-fill" style="min-width: 32mm;">{{ $issueDate }}</span>.
                </div>
            </div>

            <div class="statement-row">
                <div class="field-number">9.</div>
                <div class="statement-text">
                    His/Her conduct has been <span class="statement-fill" style="min-width: 25mm;">{{ $conduct }}</span>. I know nothing against his/her character <span class="statement-fill" style="min-width: 25mm;">{{ $characterRecord }}</span>.
                </div>
            </div>
        </section>

        <p class="blessing">I wish him/her for Bright future.</p>

        <footer class="signatures">
            <div class="signature-date">Date: {{ $issueDate }}</div>
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
