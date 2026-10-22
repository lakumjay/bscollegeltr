<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('profiles')) {
            Schema::table('profiles', function (Blueprint $table) {
                if (!Schema::hasColumn('profiles', 'university_name')) {
                    $table->string('university_name')->nullable()->after('college_name');
                }
                if (!Schema::hasColumn('profiles', 'affiliation')) {
                    $table->string('affiliation')->nullable()->after('university_name');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('profiles')) {
            Schema::table('profiles', function (Blueprint $table) {
                if (Schema::hasColumn('profiles', 'affiliation')) {
                    $table->dropColumn('affiliation');
                }
                if (Schema::hasColumn('profiles', 'university_name')) {
                    $table->dropColumn('university_name');
                }
            });
        }
    }
};
