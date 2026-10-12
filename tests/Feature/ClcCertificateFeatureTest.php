<?php

namespace Tests\Feature;

use App\Models\Courses;
use App\Models\Profile;
use App\Models\Session;
use App\Models\Student;
use App\Models\StudentCertificate;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ClcCertificateFeatureTest extends TestCase
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
            $table->string('name_in_hindi')->nullable();
            $table->string('contact_number');
            $table->string('email');
            $table->text('address')->nullable();
            $table->string('website')->nullable();
            $table->string('lc_prefix', 20)->nullable();
            $table->string('logo')->nullable();
            $table->string('banner')->nullable();
            $table->timestamps();
        });
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->timestamps();
        });
        Schema::create('student_certificate', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->text('data')->nullable();
            $table->integer('sr');
            $table->string('certificate_number', 64)->nullable()->unique();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('generated_by');
            $table->timestamps();
        });
    }

    public function test_profile_saves_the_lc_prefix_in_uppercase(): void
    {
        [$admin, $profile] = $this->fixture();

        $this->actingAs($admin)->post(route('admin.profile.storeOrUpdate'), [
            'college_name' => $profile->college_name,
            'contact_number' => $profile->contact_number,
            'email' => $profile->email,
            'lc_prefix' => 'kcb',
        ])->assertRedirect(url('admin/profile'));

        $this->assertDatabaseHas('profiles', [
            'id' => $profile->id,
            'lc_prefix' => 'KCB',
        ]);
    }

    public function test_profile_allows_saving_prefix_with_slashes(): void
    {
        [$admin, $profile] = $this->fixture();

        $this->actingAs($admin)->post(route('admin.profile.storeOrUpdate'), [
            'college_name' => $profile->college_name,
            'contact_number' => $profile->contact_number,
            'email' => $profile->email,
            'lc_prefix' => 'akc/clc',
        ])->assertRedirect(url('admin/profile'));

        $this->assertDatabaseHas('profiles', [
            'id' => $profile->id,
            'lc_prefix' => 'AKC/CLC',
        ]);
    }

    public function test_generating_a_clc_stores_the_formatted_certificate_number(): void
    {
        [$admin] = $this->fixture('KCB');
        $student = Student::create([
            'first_name' => 'Certificate',
            'last_name' => 'Student',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.students.certificate_certificate', [
            'student_id' => $student->id,
            'type' => 'clc',
        ]), [
            'data' => ['remarks' => 'Good'],
        ]);

        $certificate = DB::table('student_certificate')->first();
        $response->assertRedirect(url("admin/students/{$student->id}/clc?certId={$certificate->id}"));
        $this->assertSame(
            'KCB/' . now()->year . '/0001',
            $certificate->certificate_number
        );
    }

    public function test_generating_a_clc_without_prefix_omits_prefix_and_slash(): void
    {
        [$admin] = $this->fixture(null);
        $student = Student::create([
            'first_name' => 'NoPrefix',
            'last_name' => 'Student',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.students.certificate_certificate', [
            'student_id' => $student->id,
            'type' => 'clc',
        ]), [
            'data' => ['remarks' => 'Good'],
        ]);

        $certificate = DB::table('student_certificate')->first();
        $response->assertRedirect(url("admin/students/{$student->id}/clc?certId={$certificate->id}"));
        $this->assertSame(
            now()->year . '/0001',
            $certificate->certificate_number
        );
    }

    public function test_clc_serial_number_cannot_exceed_9999(): void
    {
        [$admin] = $this->fixture('LC');
        $student = Student::create([
            'first_name' => 'Limit',
            'last_name' => 'Student',
        ]);
        DB::table('student_certificate')->insert([
            'type' => 'clc',
            'data' => '{}',
            'sr' => 9999,
            'certificate_number' => 'LC/2026/9999',
            'student_id' => $student->id,
            'generated_by' => $admin->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $anotherStudent = Student::create([
            'first_name' => 'Second',
            'last_name' => 'Student',
        ]);

        $this->actingAs($admin)->post(route('admin.students.certificate_certificate', [
            'student_id' => $anotherStudent->id,
            'type' => 'clc',
        ]), ['data' => []])->assertSessionHasErrors('certificate');

        $this->assertSame(1, DB::table('student_certificate')->count());
    }

    public function test_classic_clc_view_renders_the_physical_certificate_fields(): void
    {
        $student = new Student([
            'first_name' => 'Anita',
            'last_name' => 'Kumari',
            'father_name' => 'Raj Kumar',
            'date_of_birth' => '2005-04-12',
            'permanent_address' => 'Tandwa, Chatra, Jharkhand',
            'roll_no' => '42',
            'student_id' => 'KCB-2026-42',
            'reg_no' => 'VBU-7788',
            'category' => 'obc',
            'batch' => '2023-2026',
            'subjects' => [],
        ]);
        $student->setRelation('course_details', new Courses(['name' => 'Bachelor of Arts']));
        $student->setRelation('session', new Session(['session_name' => '2023-2026']));

        $profile = new Profile([
            'college_name' => 'KCB College',
            'address' => 'Tandwa, Chatra, Jharkhand',
            'lc_prefix' => 'KCB',
        ]);
        $certificate = new StudentCertificate([
            'sr' => 600,
            'certificate_number' => 'KCB/2026/0009',
            'data' => json_encode([
                'registration_number' => 'VBU-7788',
                'examination_year' => '2026',
            ]),
        ]);
        $certificate->created_at = now();

        $html = view('admin.certificates.clc', [
            'student' => $student,
            'profile' => $profile,
            'StudentCertificate' => $certificate,
        ])->render();

        $this->assertStringContainsString('College Leaving / Transfer certificate', $html);
        $this->assertStringContainsString('KCB/2026/0009', $html);
        $this->assertStringContainsString('Serial No.: 00600', $html);
        $this->assertStringContainsString('Category', $html);
        $this->assertStringContainsString('OBC', $html);
        $this->assertStringContainsString('Permanent Address', $html);
        $this->assertStringContainsString('I wish him/her for Bright future.', $html);
        $this->assertStringContainsString('PRINCIPAL', $html);
    }

    public function test_generating_a_character_certificate_stores_formatted_number_with_chr_tag(): void
    {
        [$admin] = $this->fixture('KCB');
        $student = Student::create([
            'first_name' => 'Rahul',
            'last_name' => 'Sharma',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.students.certificate_certificate', [
            'student_id' => $student->id,
            'type' => 'character',
        ]), [
            'data' => ['remarks' => 'Good'],
        ]);

        $certificate = DB::table('student_certificate')->where('type', 'character')->first();
        $response->assertRedirect(url("admin/students/{$student->id}/character?certId={$certificate->id}"));
        $this->assertSame(
            'KCB/CHR/' . now()->year . '/0001',
            $certificate->certificate_number
        );
    }

    public function test_classic_character_certificate_view_renders_fields(): void
    {
        $student = new Student([
            'first_name' => 'Rahul',
            'last_name' => 'Sharma',
            'father_name' => 'Suresh Sharma',
            'permanent_address' => 'Tandwa, Chatra',
            'batch' => '2023-2026',
        ]);
        $student->setRelation('course_details', new Courses(['name' => 'Bachelor of Arts']));
        $student->setRelation('session', new Session(['session_name' => '2023-2026']));

        $profile = new Profile([
            'college_name' => 'VANANCHAL COLLEGE',
            'address' => 'TANDWA - 825321, Distt. - CHATRA (JHARKHAND)',
            'lc_prefix' => 'VC',
        ]);
        $certificate = new StudentCertificate([
            'sr' => 6,
            'certificate_number' => 'VC/CHR/2026/0006',
            'data' => json_encode([]),
        ]);
        $certificate->created_at = now();

        $html = view('admin.certificates.character', [
            'student' => $student,
            'profile' => $profile,
            'StudentCertificate' => $certificate,
        ])->render();

        $this->assertStringContainsString('CHARACTER CERTIFICATE', $html);
        $this->assertStringContainsString('VC/CHR/2026/0006', $html);
        $this->assertStringContainsString('Rahul Sharma', $html);
        $this->assertStringContainsString('Suresh Sharma', $html);
        $this->assertStringContainsString('He/She bears a good moral character', $html);
        $this->assertStringContainsString('Assistant', $html);
        $this->assertStringContainsString('PRINCIPAL', $html);
    }

    public function test_clc_certificate_is_generated_only_once_per_student(): void
    {
        [$admin] = $this->fixture('KCB');
        $student = Student::create([
            'first_name' => 'Rahul',
            'last_name' => 'Sharma',
        ]);

        $this->actingAs($admin)->post(route('admin.students.certificate_certificate', [
            'student_id' => $student->id,
            'type' => 'clc',
        ]), [
            'data' => ['registration_number' => '1111'],
        ]);

        $firstCount = DB::table('student_certificate')->where('student_id', $student->id)->where('type', 'clc')->count();
        $firstCert = DB::table('student_certificate')->where('student_id', $student->id)->where('type', 'clc')->first();

        $this->assertSame(1, $firstCount);

        // Second attempt to generate CLC should not create another record
        $response = $this->actingAs($admin)->post(route('admin.students.certificate_certificate', [
            'student_id' => $student->id,
            'type' => 'clc',
        ]), [
            'data' => ['registration_number' => '2222'],
        ]);

        $response->assertRedirect(url("admin/students/{$student->id}/clc?certId={$firstCert->id}"));
        $this->assertSame(1, DB::table('student_certificate')->where('student_id', $student->id)->where('type', 'clc')->count());
    }

    private function fixture(?string $prefix = null): array
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => Hash::make('secret'),
            'type' => 'admin',
        ]);
        $profile = Profile::create([
            'user_id' => $admin->id,
            'college_name' => 'KCB College',
            'contact_number' => '9999999999',
            'email' => 'college@example.test',
            'lc_prefix' => $prefix,
        ]);

        return [$admin, $profile];
    }
}
