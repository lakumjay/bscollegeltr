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
        Schema::table('fee_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('fee_payments', 'student_type')) {
                $table->string('student_type')->default('Regular College')->after('discount_amount');
            }
            if (!Schema::hasColumn('fee_payments', 'other_college_name')) {
                $table->string('other_college_name')->nullable()->after('student_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fee_payments', function (Blueprint $table) {
            if (Schema::hasColumn('fee_payments', 'other_college_name')) {
                $table->dropColumn('other_college_name');
            }
            if (Schema::hasColumn('fee_payments', 'student_type')) {
                $table->dropColumn('student_type');
            }
        });
    }
};
