<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Profile;
use App\Models\Role;
use App\Models\Session;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Ensure all system permissions exist
        $permissions = [
            'dashboard'            => 'Dashboard',
            'profile'              => 'Profile',
            'student-management'   => 'Student management',
            'staff-management'     => 'Staff management',
            'staff-attendance'     => 'Staff attendance',
            'library'              => 'Library',
            'salary-management'    => 'Salary management',
            'fee-management'       => 'Fee management',
            'accounts'             => 'Accounts',
            'exam-management'      => 'Exam management',
            'inventory-management' => 'Inventory management',
            'session-management'   => 'Session management',
        ];

        foreach ($permissions as $slug => $name) {
            Permission::firstOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'description' => "Access to {$name} module"]
            );
        }

        // 2. Ensure the Administrator role exists with all permissions
        $adminRole = Role::firstOrCreate(
            ['slug' => 'administrator'],
            [
                'name'        => 'Administrator',
                'description' => 'Full access to every administration module.',
            ]
        );
        $adminRole->permissions()->sync(Permission::pluck('id'));

        // 3. Ensure at least one active academic session exists
        $currentYear = date('Y');
        $nextYear = (int)$currentYear + 1;
        $session = Session::firstOrCreate(
            ['session_name' => "{$currentYear}-{$nextYear}"]
        );

        // 4. Create or update the Super Admin user
        $email = env('SUPER_ADMIN_EMAIL', 'admin@admin.com');
        $password = env('SUPER_ADMIN_PASSWORD', 'admin123');
        $name = env('SUPER_ADMIN_NAME', 'Super Admin');

        $superAdmin = User::firstOrNew(['email' => $email]);
        $superAdmin->name = $name;
        $superAdmin->password = Hash::make($password);
        $superAdmin->type = 'admin';
        $superAdmin->active_session_id = $session->id;
        $superAdmin->permissions = '';
        $superAdmin->save();

        // 5. Attach Administrator role
        $superAdmin->roles()->syncWithoutDetaching([$adminRole->id]);

        // 6. Ensure college profile exists
        Profile::firstOrCreate(
            ['user_id' => $superAdmin->id],
            [
                'college_name'   => 'AKS College',
                'contact_number' => '1234567890',
                'email'          => $email,
                'address'        => 'College Campus, Medininagar, Palamau',
                'lc_prefix'      => 'AKC/CLC',
            ]
        );

        $this->command?->info("Super Admin seeded successfully: Email: {$email} | Password: {$password}");
    }
}
