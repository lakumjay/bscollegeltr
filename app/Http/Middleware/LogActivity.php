<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog;

class LogActivity
{
    protected $routeMap = [
        // Students
        'admin.students.store'                   => ['Student_Mgt', 'Student Added'],
        'admin.students.update'                  => ['Student_Mgt', 'Student Updated'],
        'admin.students.destroy'                 => ['Student_Mgt', 'Student Deleted'],
        'admin.students.certificate_certificate' => ['Student_Mgt', 'Certificate Generated'],
        'admin.students.upload.save'             => ['Student_Mgt', 'Students Uploaded (Bulk)'],
        'admin.students.uploadAKS.save'          => ['Student_Mgt', 'Students Uploaded (AKS)'],
        'admin.students.add_session'             => ['Student_Mgt', 'Student Session Added'],

        // Courses & Subjects
        'admin.course.store'                     => ['Student_Mgt', 'Course Added'],
        'admin.course.update'                    => ['Student_Mgt', 'Course Updated'],
        'admin.course.destroy'                   => ['Student_Mgt', 'Course Deleted'],
        'admin.subject.store'                    => ['Student_Mgt', 'Subject Added'],
        'admin.subject.update'                   => ['Student_Mgt', 'Subject Updated'],
        'admin.subject.destroy'                  => ['Student_Mgt', 'Subject Deleted'],

        // Fee Structure
        'fee_structure.store'                    => ['Fee_Mgt', 'Fee Structure Saved'],
        'fee_structure2.store'                   => ['Fee_Mgt', 'Fee Structure Saved'],

        // Special Category
        'special_category.store'                 => ['Fee_Mgt', 'Special Category Added'],

        // Fee Management
        'fee_management.generate'                => ['Fee_Mgt', 'Fee Generated for Student'],
        'fee_management.add_fee'                 => ['Fee_Mgt', 'Fee Head Added'],
        'fee_management.regenerate'              => ['Fee_Mgt', 'Fee Regenerated'],
        'fee_management.pay'                     => ['Fee_Mgt', 'Fee Payment Recorded'],
        'fee_management.pay_multi'               => ['Fee_Mgt', 'Multiple Fee Payments Recorded'],

        // Staff
        'admin.staff.store'                      => ['Staff_Mgt', 'Staff Added'],
        'admin.staff.update'                     => ['Staff_Mgt', 'Staff Updated'],
        'admin.staff.destroy'                    => ['Staff_Mgt', 'Staff Deleted'],

        // Staff Attendance
        'admin.staff.attendance.markAttendance'  => ['Staff_Attendance', 'Staff Attendance Marked'],

        // Library
        'admin.library.categories.store'         => ['Library', 'Category Added'],
        'admin.library.categories.update'        => ['Library', 'Category Updated'],
        'admin.library.categories.destroy'       => ['Library', 'Category Deleted'],
        'admin.library.book.store'               => ['Library', 'Book Added'],
        'admin.library.book.update'              => ['Library', 'Book Updated'],
        'admin.library.book.destroy'             => ['Library', 'Book Deleted'],
        'admin.library.issue.store'              => ['Library', 'Book Issued'],
        'admin.library.issue.update'             => ['Library', 'Book Return Recorded'],

        // Exam
        'admin.exam.store'                       => ['Exam', 'Exam Record Added'],
        'admin.exam.update'                      => ['Exam', 'Exam Record Updated'],
        'admin.exam.destroy'                     => ['Exam', 'Exam Record Deleted'],

        // Accounts
        'admin.accounts.income.store'            => ['Accounts', 'Income Added'],
        'admin.accounts.income.update'           => ['Accounts', 'Income Updated'],
        'admin.accounts.income.destroy'          => ['Accounts', 'Income Deleted'],
        'admin.accounts.expense.store'           => ['Accounts', 'Expense Added'],
        'admin.accounts.expense.update'          => ['Accounts', 'Expense Updated'],
        'admin.accounts.expense.destroy'         => ['Accounts', 'Expense Deleted'],

        // Stock
        'admin.stock.store'                      => ['Stock', 'Stock Item Added'],
        'admin.stock.update'                     => ['Stock', 'Stock Item Updated'],
        'admin.stock.destroy'                    => ['Stock', 'Stock Item Deleted'],

        // Profile & Sessions
        'admin.profile.storeOrUpdate'            => ['Profile', 'Profile Updated'],
        'admin.sessions.activate'                => ['Session', 'Academic Session Activated'],

        // Users
        'users.store'                            => ['User_Mgt', 'New User Created'],
        'users.update'                           => ['User_Mgt', 'User Updated'],
        'users.destroy'                          => ['User_Mgt', 'User Removed'],
    ];

    // Fields to NEVER log (sensitive/useless)
    protected $skipFields = [
        '_token', '_method', 'password', 'new_password',
        'password_confirmation', 'current_password',
    ];

    // Human-readable labels for known fields
    protected $fieldLabels = [
        'first_name'        => 'First Name',
        'last_name'         => 'Last Name',
        'name'              => 'Name',
        'email'             => 'Email',
        'roll_no'           => 'Roll No',
        'course'            => 'Course',
        'batch'             => 'Batch',
        'session_id'        => 'Session ID',
        'amount'            => 'Amount (₹)',
        'payment_method'    => 'Payment Method',
        'payment_date'      => 'Payment Date',
        'transaction_id'    => 'Transaction ID',
        'receipt_no'        => 'Receipt No',
        'remarks'           => 'Remarks',
        'title'             => 'Title',
        'subject'           => 'Subject',
        'date'              => 'Date',
        'type'              => 'Type',
        'gender'            => 'Gender',
        'phone'             => 'Phone',
        'address'           => 'Address',
        'father_name'       => 'Father Name',
        'mother_name'       => 'Mother Name',
        'dob'               => 'Date of Birth',
        'category'          => 'Category',
        'status'            => 'Status',
        'permissions'       => 'Permissions',
        'designation'       => 'Designation',
        'department'        => 'Department',
        'joining_date'      => 'Joining Date',
        'salary'            => 'Salary (₹)',
        'quantity'          => 'Quantity',
        'description'       => 'Description',
        'fine'              => 'Fine Amount (₹)',
        'borrower_id'       => 'Borrower ID',
        'book_id'           => 'Book ID',
        'isbn'              => 'ISBN',
        'author'            => 'Author',
        'publisher'         => 'Publisher',
    ];

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (!Auth::check()) return $response;

        $method = strtoupper($request->method());
        if (!in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) return $response;

        $routeName = $request->route() ? $request->route()->getName() : null;
        if (in_array($routeName, ['login', 'logout', 'register', null])) return $response;

        // ── ALWAYS return the original response — logging MUST NEVER break the app ──
        try {
            // Only log successful actions
            if ($response->getStatusCode() < 400) {
                $user    = Auth::user();
                [$module, $action] = $this->resolveAction($routeName, $method, $request);
                $allData = $this->collectRequestData($request);

                ActivityLog::create([
                    'user_id'      => $user->id,
                    'user_name'    => $user->name,
                    'action'       => $action,
                    'module'       => $module,
                    'description'  => $this->buildSummary($allData, $action),
                    'url'          => $request->fullUrl(),
                    'method'       => $method,
                    'ip_address'   => $request->ip(),
                    'request_data' => json_encode($allData, JSON_UNESCAPED_UNICODE),
                ]);
            }
        } catch (\Throwable $e) {
            // Silently ignore — never break the app due to logging failure
        }

        return $response;
    }

    protected function collectRequestData(Request $request): array
    {
        $data = [];
        foreach ($request->all() as $key => $value) {
            if (in_array($key, $this->skipFields)) continue;
            $data[$key] = $this->flattenValue($value);
        }

        // Also capture route parameters (e.g. student id, user id)
        if ($request->route()) {
            foreach ($request->route()->parameters() as $k => $v) {
                if (!isset($data[$k])) {
                    $data['route_' . $k] = $this->flattenValue($v);
                }
            }
        }

        return $data;
    }

    protected function flattenValue($value): string
    {
        if (is_null($value)) return '';
        if (!is_array($value)) return (string) $value;

        // Flatten nested arrays safely to JSON
        $flat = [];
        array_walk_recursive($value, function($item) use (&$flat) {
            if (!is_null($item) && $item !== '') {
                $flat[] = $item;
            }
        });
        return implode(', ', $flat);
    }

    protected function resolveAction(?string $routeName, string $method, Request $request): array
    {
        if ($routeName && isset($this->routeMap[$routeName])) {
            return $this->routeMap[$routeName];
        }

        // Guess module from URL
        $segments = array_values(array_filter(explode('/', $request->path())));
        $seg      = $segments[1] ?? ($segments[0] ?? 'General');
        $module   = ucwords(str_replace(['-', '_'], ' ', $seg));
        $action   = match ($method) {
            'POST'          => "$module Action",
            'PUT', 'PATCH'  => "$module Updated",
            'DELETE'        => "$module Deleted",
            default         => "$module Action",
        };

        return [$module, $action];
    }

    protected function buildSummary(array $data, string $action): string
    {
        $priority = ['name','first_name','last_name','roll_no','amount','payment_method',
                     'course','email','title','date','type','subject'];
        $parts = [];
        foreach ($priority as $field) {
            if (isset($data[$field]) && $data[$field] !== '') {
                $label   = $this->fieldLabels[$field] ?? ucfirst(str_replace('_', ' ', $field));
                $parts[] = "$label: " . $data[$field];
            }
        }
        return $action . ($parts ? ' — ' . implode(', ', $parts) : '');
    }
}
