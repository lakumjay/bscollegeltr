<?php

namespace Tests\Unit;

use App\Services\ClcCertificateNumberGenerator;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ClcCertificateNumberGeneratorTest extends TestCase
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

        Schema::create('student_certificate', function (Blueprint $table) {
            $table->id();
            $table->string('certificate_number')->nullable()->unique();
            $table->timestamps();
        });
    }

    public function test_it_uses_the_profile_prefix_current_year_and_four_digit_suffix(): void
    {
        $generator = new ClcCertificateNumberGenerator(static function (): int {
            return 9;
        });

        $this->assertSame('KCB/CLC/2026/0009', $generator->generate('KCB/CLC', 2026));
    }

    public function test_it_retries_when_a_generated_number_already_exists(): void
    {
        DB::table('student_certificate')->insert([
            'certificate_number' => 'KCB/CLC/2026/0009',
        ]);

        $suffixes = [9, 10];
        $generator = new ClcCertificateNumberGenerator(static function () use (&$suffixes): int {
            return array_shift($suffixes);
        });

        $this->assertSame('KCB/CLC/2026/0010', $generator->generate('KCB/CLC', 2026));
    }
}
