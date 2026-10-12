<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\FeePayment;
use App\Models\Income;
use App\Models\Profile;
use App\Models\Session;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AccountsDaybookExportTest extends TestCase
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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('college_name');
            $table->string('contact_number');
            $table->string('email');
            $table->text('address')->nullable();
            $table->string('logo')->nullable();
            $table->longText('banner')->nullable();
            $table->timestamps();
        });
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('session_id');
            $table->string('category');
            $table->decimal('amount', 12, 2);
            $table->date('date');
            $table->text('description')->nullable();
            $table->timestamps();
        });
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('session_id');
            $table->string('category');
            $table->decimal('amount', 12, 2);
            $table->date('date');
            $table->text('description')->nullable();
            $table->timestamps();
        });
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_id')->nullable();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->timestamps();
        });
        Schema::create('fee_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('session_id');
            $table->unsignedBigInteger('student_id')->nullable();
            $table->string('student_type')->default('Regular College');
            $table->string('other_college_name')->nullable();
            $table->decimal('amount', 12, 2)->default(0);
            $table->date('payment_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function test_footer_uses_dynamic_college_name_without_vendor_credit(): void
    {
        [$admin] = $this->fixture();

        $this->actingAs($admin)
            ->get(route('accounts.daybook'))
            ->assertOk()
            ->assertSee('Dynamic Accounts College')
            ->assertDontSee('SN Web Technology')
            ->assertDontSee('snwebkarma.in', false);
    }

    public function test_daybook_csv_preserves_filters_and_student_column_order(): void
    {
        [$admin] = $this->fixture();

        $response = $this->actingAs($admin)->get(route('accounts.daybook.export_csv', [
            'from_date' => '2032-01-01',
            'to_date' => '2032-01-31',
        ]));

        $response->assertOk();
        ob_start();
        $response->baseResponse->sendContent();
        $csv = ob_get_clean();

        $this->assertStringContainsString('"Student Name","Student Category",Date,Type,Amount,"Short Description","Recorded By"', $csv);
        $this->assertStringContainsString('05-01-2032', $csv);
        $this->assertStringContainsString('January admission collection', $csv);
        $this->assertStringContainsString('Stationery purchase', $csv);
        $this->assertStringContainsString('Other College (BCD College)', $csv);
        $this->assertStringContainsString('daybook_2032-01-01_to_2032-01-31.csv', $response->headers->get('content-disposition'));
    }

    public function test_daybook_filters_out_records_outside_date_range(): void
    {
        [$admin] = $this->fixture();

        $response = $this->actingAs($admin)->get(route('accounts.daybook', [
            'from_date' => '2032-01-01',
            'to_date' => '2032-01-05',
        ]));

        $response->assertOk();
        $response->assertSee('January admission collection');
        $response->assertSee('05-01-2032');
        $response->assertDontSee('Stationery purchase');
    }

    public function test_daybook_handles_dd_mm_yyyy_date_filters(): void
    {
        [$admin] = $this->fixture();

        $response = $this->actingAs($admin)->get(route('accounts.daybook', [
            'from_date' => '01-01-2032',
            'to_date' => '05-01-2032',
        ]));

        $response->assertOk();
        $response->assertSee('value="01-01-2032"', false);
        $response->assertSee('value="05-01-2032"', false);
        $response->assertSee('05-01-2032');
        $response->assertSee('January admission collection');
        $response->assertDontSee('Stationery purchase');
    }

    public function test_daybook_web_view_displays_student_category_column_and_borrowed_college_badge(): void
    {
        [$admin] = $this->fixture();

        $response = $this->actingAs($admin)->get(route('accounts.daybook', [
            'from_date' => '2032-01-01',
            'to_date' => '2032-01-31',
        ]));

        $response->assertOk();
        $response->assertSee('Student Category');
        $response->assertSee('Other College (BCD College)');
        $response->assertSee('Rahul Sharma');
    }

    public function test_daybook_pdf_download_renders_dynamic_branding(): void
    {
        [$admin] = $this->fixture();

        $response = $this->actingAs($admin)->get(route('accounts.daybook.export_pdf', [
            'from_date' => '2032-01-01',
            'to_date' => '2032-01-31',
        ]));

        $response->assertOk();
        $this->assertSame('application/pdf', $response->headers->get('content-type'));
        $this->assertStringContainsString('daybook_2032-01-01_to_2032-01-31.pdf', $response->headers->get('content-disposition'));
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    private function fixture(): array
    {
        $session = Session::create(['session_name' => '2031-2032']);
        $admin = User::create([
            'name' => 'Accounts Admin',
            'email' => uniqid('accounts-admin-', true) . '@example.test',
            'password' => Hash::make('password'),
            'type' => 'admin',
            'active_session_id' => $session->id,
        ]);

        Profile::create([
            'user_id' => $admin->id,
            'college_name' => 'Dynamic Accounts College',
            'contact_number' => '1234567890',
            'email' => 'accounts@example.test',
            'address' => '12 Education Road, Test City',
        ]);
        Cache::forget('college_profile');

        Income::create([
            'user_id' => $admin->id,
            'session_id' => $session->id,
            'category' => 'Admission income',
            'amount' => 5000,
            'date' => '2032-01-05',
            'description' => 'January admission collection',
        ]);
        Expense::create([
            'user_id' => $admin->id,
            'session_id' => $session->id,
            'category' => 'Office supplies',
            'amount' => 750,
            'date' => '2032-01-06',
            'description' => 'Stationery purchase',
        ]);

        $student = Student::create([
            'student_id' => 'STU-9901',
            'first_name' => 'Rahul',
            'last_name' => 'Sharma',
        ]);

        FeePayment::create([
            'user_id' => $admin->id,
            'session_id' => $session->id,
            'student_id' => $student->id,
            'student_type' => 'Other College',
            'other_college_name' => 'BCD College',
            'amount' => 12000,
            'payment_date' => '2032-01-10',
            'notes' => 'Semester Fee',
        ]);

        return [$admin, $session];
    }
}
