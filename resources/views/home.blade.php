@extends('layouts.appadmin')

@section('css')
<link href="https://fonts.googleapis.com/css2?family=Crimson+Pro:wght@400;600;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
:root {
    --navy: #0b1f3a;
    --navy-mid: #142d52;
    --navy-light: #1e3d6e;
    --gold: #c9a84c;
    --gold-light: #e0c068;
    --gold-pale: rgba(201,168,76,0.1);
    --font-body: 'DM Sans', sans-serif;
    --font-serif: 'Crimson Pro', serif;
}

.dash-wrap {
    padding: 1.75rem;
    font-family: var(--font-body);
    background: #f0f2f7;
    min-height: 100%;
}

/* Page Header */
.dash-header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    margin-bottom: 1.75rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.dash-title {
    font-family: var(--font-serif);
    font-size: 2rem;
    font-weight: 600;
    color: var(--navy);
    line-height: 1;
    margin: 0;
}

.dash-subtitle {
    font-size: 0.82rem;
    color: #8a8fa3;
    margin-top: 0.3rem;
    font-weight: 300;
}

.dash-date-badge {
    background: var(--navy);
    color: rgba(255,255,255,0.85);
    padding: 0.5rem 1.1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.dash-date-badge span { color: var(--gold-light); font-weight: 500; }

/* Gold divider */
.gold-rule {
    height: 2px;
    background: linear-gradient(90deg, var(--gold), rgba(201,168,76,0.1));
    border: none;
    border-radius: 2px;
    margin-bottom: 1.75rem;
}

/* ── STAT CARDS ── */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
    gap: 1.1rem;
    margin-bottom: 1.75rem;
}

.stat-card {
    background: #fff;
    border-radius: 14px;
    padding: 1.4rem 1.25rem;
    box-shadow: 0 2px 12px rgba(0,0,0,0.055);
    position: relative;
    overflow: hidden;
    transition: transform 0.22s ease, box-shadow 0.22s ease;
    cursor: default;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 28px rgba(0,0,0,0.1);
}

/* Top accent bar */
.stat-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    border-radius: 14px 14px 0 0;
}

.stat-card.c-green::before  { background: linear-gradient(90deg, #16a34a, #4ade80); }
.stat-card.c-red::before    { background: linear-gradient(90deg, #dc2626, #f87171); }
.stat-card.c-navy::before   { background: linear-gradient(90deg, var(--navy), var(--navy-light)); }
.stat-card.c-blue::before   { background: linear-gradient(90deg, #2563eb, #60a5fa); }
.stat-card.c-teal::before   { background: linear-gradient(90deg, #0d9488, #2dd4bf); }
.stat-card.c-gold::before   { background: linear-gradient(90deg, var(--gold), var(--gold-light)); }

/* Background watermark icon */
.stat-card .bg-icon {
    position: absolute;
    bottom: -10px; right: -8px;
    font-size: 5rem;
    opacity: 0.045;
    pointer-events: none;
    line-height: 1;
}

.stat-card .sc-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.9rem;
}

.stat-icon-box {
    width: 46px; height: 46px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}

.si-green  { background: rgba(22,163,74,0.1);  }
.si-red    { background: rgba(220,38,38,0.08); }
.si-navy   { background: rgba(11,31,58,0.08);  }
.si-blue   { background: rgba(37,99,235,0.08); }
.si-teal   { background: rgba(13,148,136,0.1); }
.si-gold   { background: rgba(201,168,76,0.12);}

.stat-icon-box svg { width: 22px; height: 22px; }

.sc-trend {
    font-size: 0.7rem;
    padding: 3px 9px;
    border-radius: 10px;
    font-weight: 500;
}

.trend-up   { background: rgba(22,163,74,0.1);  color: #166534; }
.trend-down { background: rgba(220,38,38,0.08); color: #991b1b; }
.trend-neu  { background: rgba(11,31,58,0.07);  color: #374151; }

.stat-value {
    font-size: 1.75rem;
    font-weight: 600;
    color: var(--navy);
    font-family: var(--font-serif);
    line-height: 1;
    margin-bottom: 0.3rem;
}

.stat-label {
    font-size: 0.78rem;
    color: #8a8fa3;
    font-weight: 400;
}

/* ── SECTION HEADING ── */
.section-heading {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.1rem;
}

.section-heading h2 {
    font-family: var(--font-serif);
    font-size: 1.2rem;
    color: var(--navy);
    font-weight: 600;
    margin: 0;
}

.section-heading .sh-line {
    flex: 1;
    height: 1px;
    background: linear-gradient(90deg, #e8eaf0, transparent);
}

/* ── INFO CARDS ROW ── */
.info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.1rem;
    margin-bottom: 1.75rem;
}

.info-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.055);
    overflow: hidden;
}

.info-card-head {
    padding: 0.9rem 1.25rem;
    background: linear-gradient(135deg, var(--navy) 0%, var(--navy-light) 100%);
    display: flex;
    align-items: center;
    gap: 0.6rem;
}

.info-card-head h3 {
    font-family: var(--font-serif);
    font-size: 1rem;
    color: #fff;
    font-weight: 600;
    margin: 0;
}

.info-card-head .head-icon {
    width: 28px; height: 28px;
    background: rgba(201,168,76,0.2);
    border-radius: 7px;
    display: flex; align-items: center; justify-content: center;
    color: var(--gold-light);
    font-size: 0.85rem;
}

.info-card-body { padding: 0; }

.info-table { width: 100%; border-collapse: collapse; }

.info-table tr {
    border-bottom: 1px solid #f5f5f7;
    transition: background 0.15s;
}

.info-table tr:last-child { border: none; }
.info-table tr:hover { background: #fafafa; }

.info-table td {
    padding: 0.65rem 1.25rem;
    font-size: 0.81rem;
    color: #374151;
    vertical-align: middle;
}

.info-table td:first-child { color: #6b7280; width: 55%; }
.info-table td:last-child { font-weight: 500; color: var(--navy); text-align: right; }

.badge {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 10px;
    font-size: 0.68rem;
    font-weight: 500;
}

.badge-success { background: rgba(22,163,74,0.1);  color: #166534; }
.badge-warning { background: rgba(201,168,76,0.15); color: #8b6914; }
.badge-danger  { background: rgba(220,38,38,0.08);  color: #991b1b; }
.badge-info    { background: rgba(37,99,235,0.08);  color: #1d4ed8; }

/* ── QUICK ACCESS ── */
.quick-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
    gap: 0.9rem;
    margin-bottom: 1.75rem;
}

.quick-btn {
    background: #fff;
    border-radius: 12px;
    padding: 1.1rem 0.75rem;
    text-align: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    text-decoration: none !important;
    transition: all 0.2s ease;
    display: block;
    border: 1px solid transparent;
}

.quick-btn:hover {
    transform: translateY(-2px);
    border-color: rgba(201,168,76,0.35);
    box-shadow: 0 6px 20px rgba(0,0,0,0.09);
}

.quick-btn .qb-icon {
    width: 44px; height: 44px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 0.6rem;
    font-size: 1.2rem;
}

.quick-btn .qb-label {
    font-size: 0.75rem;
    font-weight: 500;
    color: var(--navy);
}

/* Progress bar */
.progress-wrap { margin-bottom: 0.65rem; }
.progress-label-row { display: flex; justify-content: space-between; margin-bottom: 0.25rem; }
.progress-label-row span { font-size: 0.75rem; color: #6b7280; }
.progress-label-row strong { font-size: 0.75rem; color: var(--navy); font-weight: 500; }
.progress-bar-bg { background: #f0f2f7; border-radius: 4px; height: 6px; overflow: hidden; }
.progress-bar-fill { height: 100%; border-radius: 4px; }

@media(max-width: 768px) {
    .stats-grid { grid-template-columns: 1fr 1fr; }
    .info-grid   { grid-template-columns: 1fr; }
    .dash-header { flex-direction: column; align-items: flex-start; }
    .quick-grid  { grid-template-columns: repeat(3,1fr); }
}

@media(max-width: 480px) {
    .stats-grid { grid-template-columns: 1fr; }
    .quick-grid { grid-template-columns: repeat(2,1fr); }
}
</style>
@endsection

@section('content')
<div class="dash-wrap">

    {{-- Page Header --}}
    <div class="dash-header">
        <div>
            <h1 class="dash-title">Dashboard {{Auth::user()->type=='admin' ? '-- All Data':'-- User Only '}}</h1>
            <p class="dash-subtitle">Welcome back, {{ Auth::user()->name }} — {{ Auth::user()->profile->college_name ?? 'AKS College' }}</p>
        </div>
        <div class="dash-date-badge">
            📅 <span>{{ date('d F Y') }}</span> &nbsp;·&nbsp; {{ date('l') }}
        </div>
    </div>

    <hr class="gold-rule">

    {{-- ── STAT CARDS ── --}}
    <div class="stats-grid">

        {{-- Gross Fee Collected --}} 
        <div class="stat-card c-green">
            <div class="bg-icon">₹</div>
            <div class="sc-top">
                <div class="stat-icon-box si-green">
                    <svg fill="none" stroke="#16a34a" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="sc-trend trend-up">Session</span>
            </div>
            <div class="stat-value">₹ {{ number_format($totalCollected) }}</div>
            <div class="stat-label">Fee Collected ({{ $sessionName }})</div>
        </div>

        {{-- Expenses --}}
        <div class="stat-card c-red">
            <div class="bg-icon">💸</div>
            <div class="sc-top">
                <div class="stat-icon-box si-red">
                    <svg fill="none" stroke="#dc2626" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <span class="sc-trend trend-down">Session</span>
            </div>
            <div class="stat-value" style="color:#dc2626;">₹ {{ number_format($totalExpense) }}</div>
            <div class="stat-label">Total Expenses ({{ $sessionName }})</div>
        </div>

        {{-- Net Income --}}
        <div class="stat-card c-navy">
            <div class="bg-icon">📈</div>
            <div class="sc-top">
                <div class="stat-icon-box si-navy">
                    <svg fill="none" stroke="#0b1f3a" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <span class="sc-trend {{ $netIncome >= 0 ? 'trend-up' : 'trend-down' }}">{{ $netIncome >= 0 ? 'Surplus' : 'Deficit' }}</span>
            </div>
            <div class="stat-value" style="color:{{ $netIncome >= 0 ? '#16a34a' : '#dc2626' }};">₹ {{ number_format($netIncome) }}</div>
            <div class="stat-label">Net Balance (Revenue − Expenses)</div>
        </div>

        {{-- Total Admissions (Active Session) --}}
        <div class="stat-card c-blue">
            <div class="bg-icon">🎓</div>
            <div class="sc-top">
                <div class="stat-icon-box si-blue">
                    <svg fill="none" stroke="#2563eb" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 005.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <span class="sc-trend trend-neu">Active Session</span>
            </div>
            <div class="stat-value">{{ number_format($totalStudents) }}</div>
            <div class="stat-label">Students in {{ $sessionName }}</div>
        </div>

        {{-- Total All Students --}}
        <div class="stat-card c-navy">
            <div class="bg-icon">👥</div>
            <div class="sc-top">
                <div class="stat-icon-box si-navy">
                    <svg fill="none" stroke="#0b1f3a" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <span class="sc-trend trend-neu">All Batches</span>
            </div>
            <div class="stat-value">{{ number_format($totalAllStudents) }}</div>
            <div class="stat-label">Total Registered Students</div>
        </div>

        {{-- Certificates Issued --}}
        <div class="stat-card c-gold">
            <div class="bg-icon">📜</div>
            <div class="sc-top">
                <div class="stat-icon-box si-gold">
                    <svg fill="none" stroke="#c9a84c" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <span class="sc-trend trend-neu">Certificates</span>
            </div>
            <div class="stat-value">{{ number_format($totalCertificates) }}</div>
            <div class="stat-label">Issued (CLC: {{ $clcCount }} | Bonafide: {{ $bonafideCount }})</div>
        </div>

        {{-- Total Employees --}}
        <div class="stat-card c-teal">
            <div class="bg-icon">👩‍🏫</div>
            <div class="sc-top">
                <div class="stat-icon-box si-teal">
                    <svg fill="none" stroke="#0d9488" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <span class="sc-trend trend-neu">Staff</span>
            </div>
            <div class="stat-value">{{ number_format($totalStaff) }}</div>
            <div class="stat-label">Total Employees</div>
        </div>

        {{-- Session Info --}}
        <div class="stat-card c-gold">
            <div class="bg-icon">📅</div>
            <div class="sc-top">
                <div class="stat-icon-box si-gold">
                    <svg fill="none" stroke="#c9a84c" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <span class="sc-trend trend-neu">Active</span>
            </div>
            <div class="stat-value" style="font-size:1.35rem;">{{ $sessionName }}</div>
            <div class="stat-label">Current Academic Session</div>
        </div>

    </div>

    {{-- ── FINANCIAL OVERVIEW ── --}}
    <div class="section-heading">
        <h2>Financial Overview — Academic Session: {{ $sessionName }}</h2>
        <div class="sh-line"></div>
    </div>

    <div class="info-grid" style="margin-bottom:1.75rem;">

        {{-- Finance Summary --}}
        <div class="info-card">
            <div class="info-card-head">
                <div class="head-icon"><i class="bx bx-bar-chart-alt-2"></i></div>
                <h3>Fee Collection Overview ({{ $sessionName }})</h3>
            </div>
            <div class="info-card-body" style="padding:1.25rem;">
                @php
                    $targetBase = $totalExpectedFee > 0 ? $totalExpectedFee : ($totalCollected > 0 ? $totalCollected : 1);
                    $collectedPct = $totalExpectedFee > 0 ? min(round(($totalCollected / $targetBase) * 100), 100) : 100;
                    $todayPct  = min(round(($todayCollection / ($totalCollected > 0 ? $totalCollected : 1)) * 100), 100);
                    $monthPct  = min(round(($monthCollection / ($totalCollected > 0 ? $totalCollected : 1)) * 100), 100);
                    $expPct    = min(round(($totalExpense / ($totalCollected > 0 ? $totalCollected : 1)) * 100), 100);
                @endphp

                <div class="progress-wrap">
                    <div class="progress-label-row">
                        <span>Total Collected (This Session)</span>
                        <strong>₹ {{ number_format($totalCollected) }} @if($totalExpectedFee > 0)<small style="color:#6b7280;">({{ $collectedPct }}% of Expected)</small>@endif</strong>
                    </div>
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill" style="width:{{ $collectedPct }}%;background:linear-gradient(90deg,#16a34a,#4ade80);"></div>
                    </div>
                </div>

                <div class="progress-wrap">
                    <div class="progress-label-row">
                        <span>This Month</span>
                        <strong>₹ {{ number_format($monthCollection) }}</strong>
                    </div>
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill" style="width:{{$monthPct}}%;background:linear-gradient(90deg,#2563eb,#60a5fa);"></div>
                    </div>
                </div>

                <div class="progress-wrap">
                    <div class="progress-label-row">
                        <span>Today</span>
                        <strong>₹ {{ number_format($todayCollection) }}</strong>
                    </div>
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill" style="width:{{$todayPct}}%;background:linear-gradient(90deg,#c9a84c,#e0c068);"></div>
                    </div>
                </div>

                <div class="progress-wrap">
                    <div class="progress-label-row">
                        <span>Expenses (This Session)</span>
                        <strong>₹ {{ number_format($totalExpense) }}</strong>
                    </div>
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill" style="width:{{$expPct}}%;background:linear-gradient(90deg,#dc2626,#f87171);"></div>
                    </div>
                </div>

                {{-- Payment Method Breakdown --}}
                @if($paymentBreakdown->isNotEmpty())
                <div style="margin-top:1rem;border-top:1px solid #f0f0f0;padding-top:0.75rem;">
                    <div style="font-size:0.75rem;color:#9ca3af;font-weight:500;margin-bottom:0.5rem;">BY PAYMENT METHOD (THIS SESSION)</div>
                    @foreach($paymentBreakdown as $pm)
                    <div style="display:flex;justify-content:space-between;font-size:0.82rem;padding:3px 0;">
                        <span>{{ $pm->payment_method ?: 'Unknown' }}</span>
                        <strong>₹ {{ number_format($pm->total) }} <span style="color:#9ca3af;">({{ $pm->cnt }})</span></strong>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        {{-- Quick Stats Table --}}
        <div class="info-card">
            <div class="info-card-head">
                <div class="head-icon"><i class="bx bx-detail"></i></div>
                <h3>Academic & Financial Summary</h3>
            </div>
            <div class="info-card-body">
                <table class="info-table">
                    <tr>
                        <td>Current Academic Session</td>
                        <td><span class="badge badge-success">{{ $sessionName }}</span></td>
                    </tr>
                    <tr>
                        <td>Total Students (This Session)</td>
                        <td>{{ number_format($totalStudents) }} <span class="badge badge-info">Active</span></td>
                    </tr>
                    <tr>
                        <td>Total Staff Members</td>
                        <td>{{ number_format($totalStaff) }}</td>
                    </tr>
                    @if($totalExpectedFee > 0)
                    <tr>
                        <td>Total Expected Fees</td>
                        <td style="color:#2563eb;font-weight:600;">₹ {{ number_format($totalExpectedFee) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td>Total Fee Collected</td>
                        <td style="color:#16a34a;font-weight:600;">₹ {{ number_format($totalCollected) }}</td>
                    </tr>
                    @if($totalDiscounts > 0)
                    <tr>
                        <td>Fee Discounts Given</td>
                        <td style="color:#6b7280;">₹ {{ number_format($totalDiscounts) }}</td>
                    </tr>
                    @endif
                    @if($totalExpectedFee > 0)
                    <tr>
                        <td>Outstanding Fee Balance</td>
                        <td style="color:#dc2626;font-weight:600;">₹ {{ number_format($totalDue) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td>Collection Today</td>
                        <td style="color:#c9a84c;">₹ {{ number_format($todayCollection) }}</td>
                    </tr>
                    <tr>
                        <td>Collection This Month</td>
                        <td style="color:#2563eb;">₹ {{ number_format($monthCollection) }}</td>
                    </tr>
                    <tr>
                        <td>Total Expenses</td>
                        <td style="color:#dc2626;">₹ {{ number_format($totalExpense) }}</td>
                    </tr>
                    <tr>
                        <td>Other Income</td>
                        <td style="color:#16a34a;">₹ {{ number_format($otherIncome) }}</td>
                    </tr>
                    <tr>
                        <td>Net Balance (Revenue − Expense)</td>
                        <td style="color:{{ $netIncome >= 0 ? '#16a34a' : '#dc2626' }};font-weight:700;">₹ {{ number_format($netIncome) }}</td>
                    </tr>
                    @foreach($courseWise as $cw)
                    <tr>
                        <td style="font-size:0.8rem;color:#6b7280;">{{ Str::limit($cw->course_name, 30) }}</td>
                        <td><strong>{{ number_format($cw->cnt) }}</strong> students</td>
                    </tr>
                    @endforeach
                </table>
            </div>
        </div>

    </div>

    {{-- ── RECENT PAYMENTS ── --}}
    @if($recentPayments->isNotEmpty())
    <div class="section-heading">
        <h2>Recent Fee Payments</h2>
        <div class="sh-line"></div>
    </div>
    <div class="info-card" style="margin-bottom:1.75rem;">
        <div class="info-card-body" style="padding:0;">
            <table class="info-table" style="margin:0;">
                <thead>
                    <tr style="background:#f8f9fc;font-size:0.75rem;color:#6b7280;text-transform:uppercase;letter-spacing:.04em;">
                        <th style="padding:.6rem 1rem;">Student</th>
                        <th style="padding:.6rem 1rem;">Amount</th>
                        <th style="padding:.6rem 1rem;">Method</th>
                        <th style="padding:.6rem 1rem;">Date</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($recentPayments as $rp)
                <tr>
                    <td style="padding:.55rem 1rem;">
                        <strong>{{ $rp->student->first_name ?? '' }} {{ $rp->student->last_name ?? 'N/A' }}</strong>
                        @if($rp->student && $rp->student->roll_no)
                        <small style="display:block;color:#9ca3af;font-size:0.72rem;">Roll: {{ $rp->student->roll_no }}</small>
                        @endif
                    </td>
                    <td style="padding:.55rem 1rem;color:#16a34a;font-weight:600;">₹ {{ number_format($rp->amount) }}</td>
                    <td style="padding:.55rem 1rem;">
                        <span style="background:#f0f9ff;color:#0369a1;padding:2px 8px;border-radius:8px;font-size:0.75rem;">{{ $rp->payment_method }}</span>
                    </td>
                    <td style="padding:.55rem 1rem;color:#9ca3af;font-size:0.8rem;">
                        {{ $rp->payment_date ? \Carbon\Carbon::parse($rp->payment_date)->format('d-m-Y') : \Carbon\Carbon::parse($rp->created_at)->format('d-m-Y') }}
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ── MONTHLY COLLECTION CHART ── --}}
    <div class="section-heading">
        <h2>Monthly Fee Collection (Last 6 Months)</h2>
        <div class="sh-line"></div>
    </div>
    <div class="info-card" style="margin-bottom:1.75rem;">
        <div class="info-card-body" style="padding:1.25rem;">
            <canvas id="monthlyChart" style="max-height:240px;"></canvas>
        </div>
    </div>

    {{-- ── QUICK ACCESS ── --}}
    <div class="section-heading">
        <h2>Quick Access</h2>
        <div class="sh-line"></div>
    </div>

    <div class="quick-grid">
        <a href="{{ url('admin/students') }}" class="quick-btn">
            <div class="qb-icon" style="background:rgba(37,99,235,0.08);">🎓</div>
            <div class="qb-label">Students</div>
        </a>
        <a href="{{ url('admin/students/strength') }}" class="quick-btn">
            <div class="qb-icon" style="background:rgba(201,168,76,0.12);">📊</div>
            <div class="qb-label">Strength</div>
        </a>
        <a href="{{ url('admin/staff') }}" class="quick-btn">
            <div class="qb-icon" style="background:rgba(13,148,136,0.1);">👩‍🏫</div>
            <div class="qb-label">Staff</div>
        </a>
        <a href="{{ url('admin/fee/management') }}" class="quick-btn">
            <div class="qb-icon" style="background:rgba(22,163,74,0.1);">💰</div>
            <div class="qb-label">Fee Mgt.</div>
        </a>
        <a href="{{ url('admin/accounts/income') }}" class="quick-btn">
            <div class="qb-icon" style="background:rgba(201,168,76,0.12);">📈</div>
            <div class="qb-label">Accounts</div>
        </a>
        {{-- Exams and Library modules are temporarily hidden
        <a href="{{ url('admin/exams') }}" class="quick-btn">
            <div class="qb-icon" style="background:rgba(139,92,246,0.1);">✏️</div>
            <div class="qb-label">Exams</div>
        </a>
        <a href="{{ url('admin/library/book') }}" class="quick-btn">
            <div class="qb-icon" style="background:rgba(11,31,58,0.07);">📚</div>
            <div class="qb-label">Library</div>
        </a>
        --}}
        <a href="{{ route('admin.staff.attendance.markAttendancePre') }}" class="quick-btn">
            <div class="qb-icon" style="background:rgba(220,38,38,0.07);">📅</div>
            <div class="qb-label">Attendance</div>
        </a>
        <a href="{{ url('admin/sessions') }}" class="quick-btn">
            <div class="qb-icon" style="background:rgba(201,168,76,0.12);">⏱️</div>
            <div class="qb-label">Sessions</div>
        </a>
    </div>

</div>
@endsection

@section('scripts')
<script>
(function() {
    var labels = @json($chartLabels);
    var data   = @json($chartData);

    var ctx = document.getElementById('monthlyChart');
    if (!ctx) return;

    new Chart(ctx.getContext('2d'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Fee Collected (₹)',
                data: data,
                backgroundColor: 'rgba(11,31,58,0.75)',
                borderColor: '#0b1f3a',
                borderWidth: 1,
                borderRadius: 5,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            return ' ₹ ' + Number(ctx.parsed.y).toLocaleString('en-IN');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(v) { return '₹' + Number(v).toLocaleString('en-IN'); },
                        font: { size: 11 }
                    },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: {
                    ticks: { font: { size: 12 } },
                    grid: { display: false }
                }
            }
        }
    });
})();
</script>
@endsection