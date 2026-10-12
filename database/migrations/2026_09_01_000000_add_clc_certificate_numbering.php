<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClcCertificateNumbering extends Migration
{
    public function up()
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('lc_prefix', 20)->nullable()->after('website');
        });

        Schema::table('student_certificate', function (Blueprint $table) {
            $table->string('certificate_number', 64)->nullable()->unique()->after('sr');
        });
    }

    public function down()
    {
        Schema::table('student_certificate', function (Blueprint $table) {
            $table->dropUnique(['certificate_number']);
            $table->dropColumn('certificate_number');
        });

        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn('lc_prefix');
        });
    }
}
