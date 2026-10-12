<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('fee_payments', 'discount_eligible')) {
            Schema::table('fee_payments', function (Blueprint $table): void {
                $table->boolean('discount_eligible')->default(false);
            });
        }

        if (!Schema::hasColumn('fee_payments', 'discount_amount')) {
            Schema::table('fee_payments', function (Blueprint $table): void {
                $table->decimal('discount_amount', 12, 2)->default(0);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('fee_payments', 'discount_eligible')) {
            Schema::table('fee_payments', function (Blueprint $table): void {
                $table->dropColumn('discount_eligible');
            });
        }

        if (Schema::hasColumn('fee_payments', 'discount_amount')) {
            Schema::table('fee_payments', function (Blueprint $table): void {
                $table->dropColumn('discount_amount');
            });
        }
    }
};
