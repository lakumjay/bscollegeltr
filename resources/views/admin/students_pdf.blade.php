<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 8px; color: #111; padding: 12px; }

    .header { text-align: center; margin-bottom: 8px; border-bottom: 2px solid #1a1a6e; padding-bottom: 4px; }
    .header h2 { font-size: 13px; color: #1a1a6e; font-weight: bold; }
    .header p  { font-size: 8px; color: #555; margin-top: 1px; }

    .filter-chip {
        background: #e8ecff; border: 1px solid #1a1a6e;
        border-radius: 3px; padding: 3px 6px; color: #1a1a6e;
        display: inline-block; margin-right: 4px; font-size: 8px;
    }
    .summary { font-size: 8px; color: #333; margin: 6px 0; }

    /* Explicit styling for DomPDF layout stability */
    table.data-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
    
    table.data-table thead tr { background: #1a1a6e; color: white; }
    table.data-table thead th { padding: 4px; font-size: 8px; font-weight: bold; text-align: left; border: 1px solid #1a1a6e; }
    
    /* Clean CSS classes applied directly to table rows */
    .row-odd-main  { background: #ffffff; }
    .row-odd-sub   { background: #fdfdfd; border-bottom: 2px solid #b0b8df; }
    .row-even-main { background: #f4f6ff; }
    .row-even-sub  { background: #ecefff; border-bottom: 2px solid #b0b8df; }

    table.data-table td { padding: 4px; border: 1px solid #dde2ff; font-size: 7.5px; vertical-align: top; word-wrap: break-word; }
    
    .badge { display: inline-block; padding: 1px 4px; border-radius: 2px; font-size: 7px; font-weight: bold; text-transform: capitalize; }
    .badge-male   { background: #d0eaff; color: #005a9e; }
    .badge-female { background: #ffe0f0; color: #8b0057; }
    .badge-other  { background: #e8f5e9; color: #1b5e20; }
    
    .doc-status { font-weight: bold; }
    .doc-yes { color: green; }
    .doc-no { color: red; }

    .footer { margin-top: 15px; font-size: 7px; color: #888; text-align: right; }
</style>
</head>
<body>

<div class="header">
    <h2>Student Comprehensive Master Export Report</h2>
    <p>Academic Session: {{ $sessionName }} &nbsp;|&nbsp; Run Date: {{ now()->format('d M Y, h:i A') }}</p>
</div>

<div style="margin-bottom: 6px;">
    <span class="filter-chip"><strong>Course:</strong> {{ $filters['course'] }}</span>
    <span class="filter-chip"><strong>Batch:</strong> {{ $filters['batch'] }}</span>
    <span class="filter-chip"><strong>Semester:</strong> {{ $filters['semester'] }}</span>
    <span class="filter-chip"><strong>Subject Filter:</strong> {{ $filters['subject'] }}</span>
</div>

<p class="summary">Total Cumulative Records: <strong>{{ $students->count() }}</strong></p>

<table class="data-table">
    <thead>
        <tr>
            <th style="width: 3%;">#</th>
            <th style="width: 11%;">Registration / ID</th>
            <th style="width: 6%;">Roll No.</th>
            <th style="width: 15%;">Student details</th>
            <th style="width: 13%;">Contact & family</th>
            <th style="width: 12%;">Course details</th>
            <th style="width: 13%;">Academic status</th>
            <th style="width: 15%;">Subjects & addresses</th>
            <th style="width: 12%;">Documents</th>
        </tr>
    </thead>
    <tbody>
        @forelse($students as $i => $s)
            <tr class="{{ $loop->even ? 'row-even-main' : 'row-odd-main' }}">
                <td style="text-align:center; font-weight:bold;">{{ $i + 1 }}</td>
                <td>
                    <strong>Reg:</strong> {{ $s->reg_no ?? '-' }}<br>
                    <strong>ID:</strong> {{ $s->student_id ?? '-' }}
                </td>
                <td><strong>{{ $s->roll_no ?? '-' }}</strong></td>
                <td>
                    <span style="font-size: 8.5px; font-weight: bold;">{{ $s->first_name }} {{ $s->last_name }}</span><br>
                    <strong>Gender:</strong> 
                    <span class="badge badge-{{ strtolower($s->gender ?? 'other') }}">{{ $s->gender ?? '-' }}</span><br>
                    <strong>DOB:</strong> {{ $s->date_of_birth instanceof \Carbon\Carbon ? $s->date_of_birth->format('d-m-Y') : $s->date_of_birth }}<br>
                    <strong>Category:</strong> {{ $s->category ?? 'N/A' }} / {{ $s->caste ?? 'N/A' }}<br>
                    <strong>Special category:</strong> {{ $s->special_category ?? 'None' }}
                </td>
                <td>
                    <strong>Phone:</strong> {{ $s->phone_number }}<br>
                    <strong>Email:</strong> <span style="font-size: 7px;">{{ $s->email }}</span><br>
                    <strong>Father:</strong> {{ $s->father_name }}<br>
                    <strong>Mother:</strong> {{ $s->mother_name }}
                </td>
                <td>
                    <strong>Course:</strong> {{ $s->course_details->name ?? $s->course }}<br>
                    <strong>Batch:</strong> {{ $s->batch }}<br>
                    <strong>Session:</strong> {{ optional($s->session)->session_name ?? '-' }}
                </td>
                <td>
                    <strong>Year:</strong> {{ $s->current_year }}<br>
                    <strong>Semester:</strong> {{ $s->current_semester }}<br>
                    <strong>Adm Date:</strong> {{ $s->admission_date ? (\Carbon\Carbon::parse($s->admission_date)->format('d-m-Y')) : '-' }}<br>
                    <strong>Status:</strong> <span style="font-weight: bold; color: #005a9e;">{{ $s->status ?? 'Active' }}</span>
                </td>
                <td style="font-size:7px; line-height:1.25;">
                    <strong>Subjects:</strong> {{ $s->formatted_subjects }}<br><br>
                    <strong>Permanent:</strong> {{ $s->permanent_address ?? 'N/A' }}<br>
                    <strong>Correspondence:</strong> {{ $s->correspondence_address ?? 'N/A' }}
                </td>
                <td>
                    <strong>Aadhar:</strong> 
                    <span class="doc-status {{ $s->aadhar_card ? 'doc-yes' : 'doc-no' }}">{{ $s->aadhar_card ? '✓ Yes' : '✗ No' }}</span><br>
                    <strong>ID Proof:</strong> 
                    <span class="doc-status {{ $s->id_proof ? 'doc-yes' : 'doc-no' }}">{{ $s->id_proof ? '✓ Yes' : '✗ No' }}</span><br>
                    <strong>Cat. Cert:</strong> 
                    <span class="doc-status {{ $s->category_certificate ? 'doc-yes' : 'doc-no' }}">{{ $s->category_certificate ? '✓ Yes' : '✗ No' }}</span><br>
                    <strong>Photo:</strong> <span class="doc-status {{ $s->photo ? 'doc-yes' : 'doc-no' }}">{{ $s->photo ? '✓ Yes' : '✗ No' }}</span>
                </td>
            </tr>
        @empty
        <tr>
            <td colspan="9" style="text-align:center; padding: 30px; color:#888; font-size: 11px;">
                No matching student registration data records tracked inside system filters.
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

<div class="footer">
    AKS College Management Portal System &nbsp;|&nbsp; Automated Data Stream Print Page
</div>

</body>
</html>
