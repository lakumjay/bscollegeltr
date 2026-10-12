<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddComponentSnapshotsToSalaryPayments extends Migration
{
    public function up()
    {
        $addManualAllowances = !Schema::hasColumn('salary_payments', 'manual_allowances');

        Schema::table('salary_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('salary_payments', 'manual_allowances')) {
                $table->decimal('manual_allowances', 12, 2)->nullable()->after('remarks');
            }
            if (!Schema::hasColumn('salary_payments', 'manual_deductions')) {
                $table->decimal('manual_deductions', 12, 2)->nullable()->after('manual_allowances');
            }

            foreach ([
                'basic_salary', 'da', 'hra', 'additional_earnings', 'epf',
                'medical_deduction', 'additional_deductions', 'attendance_deduction',
                'total_income', 'total_deductions', 'net_salary', 'daily_rate',
            ] as $column) {
                if (!Schema::hasColumn('salary_payments', $column)) {
                    $table->decimal($column, 12, 2)->nullable();
                }
            }

            foreach ([
                'working_days', 'present_days', 'absent_days', 'leave_days',
                'paid_leave_days', 'unpaid_leave_days', 'holiday_days',
            ] as $column) {
                if (!Schema::hasColumn('salary_payments', $column)) {
                    $table->unsignedSmallInteger($column)->nullable();
                }
            }

            if (!Schema::hasColumn('salary_payments', 'snapshot_version')) {
                $table->unsignedTinyInteger('snapshot_version')->default(0);
            }
        });

        if ($addManualAllowances && Schema::hasColumn('salary_payments', 'manual_allounces')) {
            DB::table('salary_payments')->update([
                'manual_allowances' => DB::raw('manual_allounces'),
            ]);
        } elseif (Schema::hasColumn('salary_payments', 'manual_allounces')) {
            DB::table('salary_payments')
                ->whereNull('manual_allowances')
                ->update(['manual_allowances' => DB::raw('manual_allounces')]);
        }

        // The old records only retained a final amount. Preserve it as a consolidated
        // historical snapshot instead of guessing components from today's structure.
        DB::table('salary_payments')
            ->where('snapshot_version', 0)
            ->update([
                'manual_allowances' => DB::raw('COALESCE(manual_allowances, 0)'),
                'manual_deductions' => DB::raw('COALESCE(manual_deductions, 0)'),
                'total_income' => DB::raw('amount + COALESCE(manual_deductions, 0)'),
                'total_deductions' => DB::raw('COALESCE(manual_deductions, 0)'),
                'net_salary' => DB::raw('amount'),
            ]);
    }

    public function down()
    {
        if (Schema::hasColumn('salary_payments', 'manual_allounces') && Schema::hasColumn('salary_payments', 'manual_allowances')) {
            DB::table('salary_payments')
                ->whereNull('manual_allounces')
                ->update(['manual_allounces' => DB::raw('manual_allowances')]);
        }

        $columns = collect([
            'manual_allowances', 'basic_salary', 'da', 'hra', 'additional_earnings',
            'epf', 'medical_deduction', 'additional_deductions', 'attendance_deduction',
            'total_income', 'total_deductions', 'net_salary', 'daily_rate',
            'working_days', 'present_days', 'absent_days', 'leave_days',
            'paid_leave_days', 'unpaid_leave_days', 'holiday_days', 'snapshot_version',
        ])->filter(fn ($column) => Schema::hasColumn('salary_payments', $column))->all();

        if ($columns) {
            Schema::table('salary_payments', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
}
