<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Profile;
use App\Models\Role;
use App\Models\Session;
use App\Models\User;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SuperAdminSeederAndProtectionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);
        DB::purge('mysql');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        Schema::create('sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_name');
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('type')->nullable();
            $table->unsignedBigInteger('active_session_id')->nullable();
            $table->text('permissions')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

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
            $table->foreignId('permission_id');
            $table->foreignId('role_id');
            $table->timestamps();
            $table->primary(['permission_id', 'role_id']);
        });

        Schema::create('role_user', function (Blueprint $table) {
            $table->foreignId('role_id');
            $table->foreignId('user_id');
            $table->timestamps();
            $table->primary(['role_id', 'user_id']);
        });

        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('college_name');
            $table->string('name_in_hindi')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('website')->nullable();
            $table->string('lc_prefix')->nullable();
            $table->string('logo')->nullable();
            $table->longText('banner')->nullable();
            $table->timestamps();
        });
    }

    public function test_super_admin_seeder_creates_admin_user_role_and_permissions(): void
    {
        $seeder = new SuperAdminSeeder();
        $seeder->run();

        $superAdmin = User::where('email', 'admin@admin.com')->first();
        $this->assertNotNull($superAdmin);
        $this->assertSame('Super Admin', $superAdmin->name);
        $this->assertTrue($superAdmin->isAdmin());
        $this->assertTrue($superAdmin->isSuperAdmin());
        $this->assertTrue(Hash::check('admin123', $superAdmin->password));

        // Check roles and permissions
        $this->assertTrue($superAdmin->roles->contains('slug', 'administrator'));
        $this->assertTrue($superAdmin->hasPermission('dashboard'));
        $this->assertTrue($superAdmin->hasPermission('accounts'));
        $this->assertTrue($superAdmin->hasPermission('student-management'));

        // Check active session & profile
        $this->assertNotNull($superAdmin->active_session_id);
        $this->assertNotNull(Profile::where('user_id', $superAdmin->id)->first());
    }

    public function test_super_admin_cannot_be_deleted_via_user_management_controller(): void
    {
        $seeder = new SuperAdminSeeder();
        $seeder->run();
        $superAdmin = User::where('email', 'admin@admin.com')->first();

        // Create another admin user attempting to delete super admin
        $anotherAdmin = User::create([
            'name' => 'Secondary Admin',
            'email' => 'secondary@admin.com',
            'password' => Hash::make('password123'),
            'type' => 'admin',
            'active_session_id' => $superAdmin->active_session_id,
        ]);

        $response = $this->actingAs($anotherAdmin)->post(route('users.destroy', $superAdmin));
        $response->assertStatus(403);

        // Verify super admin still exists in database
        $this->assertNotNull(User::where('email', 'admin@admin.com')->first());
    }

    public function test_super_admin_model_deletion_throws_exception(): void
    {
        $seeder = new SuperAdminSeeder();
        $seeder->run();
        $superAdmin = User::where('email', 'admin@admin.com')->first();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Super Administrator accounts cannot be deleted.');

        $superAdmin->delete();
    }

    public function test_standard_user_can_be_deleted_by_admin(): void
    {
        $seeder = new SuperAdminSeeder();
        $seeder->run();
        $superAdmin = User::where('email', 'admin@admin.com')->first();

        $role = Role::create(['name' => 'Accountant', 'slug' => 'accountant']);
        $normalUser = User::create([
            'name' => 'Staff Member',
            'email' => 'staff@example.test',
            'password' => Hash::make('password123'),
            'type' => 'user',
            'active_session_id' => $superAdmin->active_session_id,
        ]);
        $normalUser->roles()->attach($role->id);

        $response = $this->actingAs($superAdmin)->post(route('users.destroy', $normalUser));
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertNull(User::find($normalUser->id));
    }
}
