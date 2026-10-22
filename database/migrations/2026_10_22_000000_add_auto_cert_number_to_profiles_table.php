<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAutoCertNumberToProfilesTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('profiles') && !Schema::hasColumn('profiles', 'auto_cert_number')) {
            Schema::table('profiles', function (Blueprint $table) {
                $table->boolean('auto_cert_number')->default(true)->after('lc_prefix');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('profiles') && Schema::hasColumn('profiles', 'auto_cert_number')) {
            Schema::table('profiles', function (Blueprint $table) {
                $table->dropColumn('auto_cert_number');
            });
        }
    }
}
