<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ExpandSalaryStructuresForComponentPayroll extends Migration
{
    public function up()
    {
        $addAdditionalEarnings = !Schema::hasColumn('salary_structures', 'additional_earnings');
        $addAdditionalDeductions = !Schema::hasColumn('salary_structures', 'additional_deductions');

        Schema::table('salary_structures', function (Blueprint $table) {
            if (!Schema::hasColumn('salary_structures', 'da')) {
                $table->decimal('da', 12, 2)->default(0)->after('basic_pay');
            }
            if (!Schema::hasColumn('salary_structures', 'hra')) {
                $table->decimal('hra', 12, 2)->default(0)->after('da');
            }
            if (!Schema::hasColumn('salary_structures', 'additional_earnings')) {
                $table->decimal('additional_earnings', 12, 2)->default(0)->after('hra');
            }
            if (!Schema::hasColumn('salary_structures', 'epf')) {
                $table->decimal('epf', 12, 2)->default(0)->after('allowances');
            }
            if (!Schema::hasColumn('salary_structures', 'medical_deduction')) {
                $table->decimal('medical_deduction', 12, 2)->default(0)->after('epf');
            }
            if (!Schema::hasColumn('salary_structures', 'additional_deductions')) {
                $table->decimal('additional_deductions', 12, 2)->default(0)->after('medical_deduction');
            }
        });

        if ($addAdditionalEarnings && Schema::hasColumn('salary_structures', 'allowances')) {
            DB::table('salary_structures')->update([
                'additional_earnings' => DB::raw('COALESCE(allowances, 0)'),
            ]);
        }

        if ($addAdditionalDeductions && Schema::hasColumn('salary_structures', 'deductions')) {
            DB::table('salary_structures')->update([
                'additional_deductions' => DB::raw('COALESCE(deductions, 0)'),
            ]);
        }
    }

    public function down()
    {
        $columns = collect([
            'da',
            'hra',
            'additional_earnings',
            'epf',
            'medical_deduction',
            'additional_deductions',
        ])->filter(fn ($column) => Schema::hasColumn('salary_structures', $column))->all();

        if ($columns) {
            Schema::table('salary_structures', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
}
