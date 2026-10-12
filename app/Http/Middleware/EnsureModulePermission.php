<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureModulePermission
{
    /**
     * Keep module authorization in one place. Route controllers remain
     * responsible for record-level rules, while this gate prevents users
     * from opening a module by manually entering its URL.
     */
    public function handle(Request $request, Closure $next)
    {
        $permission = $this->permissionFor($request);

        if ($permission && ! $request->user()?->hasPermission($permission)) {
            abort(403, 'You do not have permission to access this module.');
        }

        return $next($request);
    }

    private function permissionFor(Request $request): ?string
    {
        return match (true) {
            $request->is('admin/profile*') => 'profile',
            $request->is('admin/sessions*') => 'session-management',
            $request->is('admin/students*'), $request->is('admin/course*'), $request->is('admin/courses/subjects*'), $request->is('admin/certificate-create*') => 'student-management',
            $request->is('admin/staff/attendance*') => 'staff-attendance',
            $request->is('admin/staff/*/salary*'), $request->is('admin/staff/salary*') => 'salary-management',
            $request->is('admin/staff*') => 'staff-management',
            $request->is('admin/fee*') => 'fee-management',
            $request->is('admin/accounts*') => 'accounts',
            $request->is('admin/inventory*'), $request->is('admin/stock*') => 'inventory-management',
            $request->is('admin/exam*'), $request->is('admin/marks*') => 'exam-management',
            $request->is('admin/library*') => 'library',
            default => null,
        };
    }
}
