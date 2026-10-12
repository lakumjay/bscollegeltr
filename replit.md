# Power College Management System

A comprehensive web-based college management application built with Laravel 8 (PHP).

## Project Overview

The **AKS College Management System** manages all aspects of a college/educational institution including academics, students, staff, finances, attendance, and library operations.

## Tech Stack

- **Backend**: PHP 8.2 / Laravel 8.75
- **Frontend**: Blade templates + Bootstrap 5 + Vue.js 2.6
- **Database**: PostgreSQL (Replit's built-in DB)
- **Assets**: Laravel Mix (Webpack)

## Features

- Student & Staff Administration
- Academic Records (Courses, Subjects, Exams, Grading, Marks)
- **Fee Management** (fully enhanced):
  - Semester-based dynamic fee head checkboxes via AJAX
  - Auto-calculated totals with editable amounts
  - Per-fee-head payment records in DB
  - Batch receipt (2-copy: Student + College, scissor cut line) printed immediately after payment
  - Single receipt print per payment row
  - Rich report with filters: fee head, student name, semester, payment method, date range
  - CSV export from report
  - `PAYMENT_METHODS` constant — add new methods in one place
- Attendance Tracking
- Salary Management
- Library Management (Books, Authors, Issuing)
- Inventory/Stock Management
- Certificate Generation (CLC, Character, Bonafide)
- Income & Expense Tracking

## Fee Management Architecture

### How to add a new payment method
Edit `FeeManagementController::PAYMENT_METHODS` — it appears automatically in dropdowns and validation.

### How to add a new report filter
1. Add a query param to the GET form in `fee_report.blade.php`
2. Add the filter logic in `FeeManagementController::report()` — the pattern is clear and consistent.

### How to add a new receipt field
Edit `resources/views/admin/fee/fee_reciept_single.blade.php` — standalone HTML, no layout dependencies.

### Key files
| File | Purpose |
|---|---|
| `FeeManagementController.php` | All fee logic — clean, commented, private helpers |
| `fee_view.blade.php` | Student fee page + payment form + AJAX |
| `fee_report.blade.php` | Filtered report + summary cards + CSV export |
| `fee_reciept_single.blade.php` | 2-copy print receipt (Student + College) |

## Project Structure

```
/
├── app/                  # Laravel application code
│   ├── Http/Controllers/ # Request handlers
│   └── Models/           # Eloquent models
├── config/               # App configuration
├── database/migrations/  # DB schema migrations
├── public/               # Web root (static assets, compiled JS/CSS)
├── resources/views/      # Blade templates
├── routes/               # Route definitions (web.php, api.php)
├── storage/              # Logs, sessions, cache
├── start.sh              # Startup script (generates .env, runs migrations, starts server)
└── server.php            # PHP built-in server router
```

## Running the App

The app starts via `bash start.sh` which:
1. Generates `.env` file from environment variables
2. Runs database migrations
3. Starts PHP development server on `0.0.0.0:5000`

## Environment Variables

Set in Replit secrets/env vars:
- `APP_KEY` - Laravel application key
- `APP_NAME`, `APP_ENV`, `APP_DEBUG`, `APP_URL`
- `DB_CONNECTION=pgsql`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`
- `PGPASSWORD` - PostgreSQL password (runtime secret)

## Database

Uses Replit's built-in PostgreSQL database. Migrations run automatically on startup.

## Deployment

Configured for autoscale deployment using `bash start.sh`.
