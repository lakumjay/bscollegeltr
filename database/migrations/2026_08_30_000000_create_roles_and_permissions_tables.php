<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('permission_role', function (Blueprint $table) {
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->primary(['permission_id', 'role_id']);
        });

        Schema::create('role_user', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->primary(['role_id', 'user_id']);
        });

        $definitions = [
            'dashboard' => 'Dashboard', 'profile' => 'Profile',
            'student-management' => 'Student management', 'staff-management' => 'Staff management',
            'staff-attendance' => 'Staff attendance', 'library' => 'Library',
            'salary-management' => 'Salary management', 'fee-management' => 'Fee management',
            'accounts' => 'Accounts', 'exam-management' => 'Exam management',
            'inventory-management' => 'Inventory management', 'session-management' => 'Session management',
        ];

        foreach ($definitions as $slug => $name) {
            Permission::create(['name' => $name, 'slug' => $slug]);
        }

        $adminRole = Role::create([
            'name' => 'Administrator',
            'slug' => 'administrator',
            'description' => 'Full access to every administration module.',
        ]);
        $adminRole->permissions()->sync(Permission::pluck('id'));

        $legacyMap = [
            'Dashboard' => 'dashboard', 'Profile' => 'profile', 'Student_Mgt' => 'student-management',
            'Staff_Mgt' => 'staff-management', 'Staff_Attendance' => 'staff-attendance', 'Library' => 'library',
            'Salary_Mgt' => 'salary-management', 'Fee_Mgt' => 'fee-management', 'Accounts' => 'accounts',
            'Exam' => 'exam-management', 'Stock' => 'inventory-management', 'Session' => 'session-management',
        ];

        User::where('type', 'admin')->each(function (User $user) use ($adminRole) {
            $user->roles()->syncWithoutDetaching([$adminRole->id]);
        });

        if (!Schema::hasColumn('users', 'permissions')) {
            return;
        }

        User::where('type', '!=', 'admin')->whereNotNull('permissions')->each(function (User $user) use ($legacyMap) {
            $legacy = collect(explode(',', (string) $user->permissions))->map(fn ($value) => trim($value))->filter();
            $slugs = $legacy->map(fn ($value) => $legacyMap[$value] ?? null)->filter()->sort()->values();
            if ($slugs->isEmpty()) {
                return;
            }

            $hash = substr(sha1($slugs->join('|')), 0, 10);
            $role = Role::firstOrCreate(
                ['slug' => 'legacy-access-' . $hash],
                ['name' => 'Migrated access ' . strtoupper(substr($hash, 0, 6)), 'description' => 'Migrated from the previous user permission settings.']
            );
            $role->permissions()->sync(Permission::whereIn('slug', $slugs)->pluck('id'));
            $user->roles()->syncWithoutDetaching([$role->id]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
