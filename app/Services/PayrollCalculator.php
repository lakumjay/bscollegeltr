<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\SalaryPayment;
use App\Models\SalaryStructure;
use Carbon\CarbonImmutable;

class PayrollCalculator
{
    public const WORKING_DAYS = 22;
    public const PAID_LEAVE_LIMIT = 2;

    public function calculateForStructure(
        SalaryStructure $structure,
        string $monthYear,
        float $manualAllowances = 0,
        float $manualDeductions = 0
    ): array {
        $month = CarbonImmutable::createFromFormat('!Y-m', $monthYear);
        $attendance = Attendance::where('staff_id', $structure->staff_id)
            ->whereBetween('date', [$month->startOfMonth()->toDateString(), $month->endOfMonth()->toDateString()])
            ->get()
            ->countBy('status');

        return $this->calculateComponents([
            'basic_salary' => $structure->basic_pay,
            'da' => $structure->da,
            'hra' => $structure->hra,
            'additional_earnings' => $structure->additional_earnings,
            'epf' => $structure->epf,
            'medical_deduction' => $structure->medical_deduction,
            'additional_deductions' => $structure->additional_deductions,
            'currency' => $structure->currency,
        ], [
            'present_days' => $attendance->get('Present', 0),
            'absent_days' => $attendance->get('Absent', 0),
            'leave_days' => $attendance->get('Leave', 0),
            'holiday_days' => $attendance->get('Holiday', 0),
        ], $manualAllowances, $manualDeductions);
    }

    public function calculateComponents(
        array $structure,
        array $attendance = [],
        float $manualAllowances = 0,
        float $manualDeductions = 0
    ): array {
        $basic = $this->money($structure['basic_salary'] ?? $structure['basic_pay'] ?? 0);
        $da = $this->money($structure['da'] ?? 0);
        $hra = $this->money($structure['hra'] ?? 0);
        $additionalEarnings = $this->money($structure['additional_earnings'] ?? 0);
        $epf = $this->money($structure['epf'] ?? 0);
        $medical = $this->money($structure['medical_deduction'] ?? 0);
        $additionalDeductions = $this->money($structure['additional_deductions'] ?? 0);
        $manualAllowances = $this->money($manualAllowances);
        $manualDeductions = $this->money($manualDeductions);

        $presentDays = (int) ($attendance['present_days'] ?? 0);
        $absentDays = (int) ($attendance['absent_days'] ?? 0);
        $leaveDays = (int) ($attendance['leave_days'] ?? 0);
        $holidayDays = (int) ($attendance['holiday_days'] ?? 0);
        $paidLeaveDays = min($leaveDays, self::PAID_LEAVE_LIMIT);
        $unpaidLeaveDays = max(0, $leaveDays - self::PAID_LEAVE_LIMIT);
        $rawDailyRate = $basic / self::WORKING_DAYS;
        $dailyRate = $this->money($rawDailyRate);
        $attendanceDeduction = $this->money(($absentDays + $unpaidLeaveDays) * $rawDailyRate);

        $recurringIncome = $this->money($basic + $da + $hra + $additionalEarnings);
        $totalIncome = $this->money($recurringIncome + $manualAllowances);
        $standardDeductions = $this->money($epf + $medical + $additionalDeductions);
        $totalDeductions = $this->money($standardDeductions + $attendanceDeduction + $manualDeductions);
        $netSalary = $this->money($totalIncome - $totalDeductions);

        return [
            'amount' => $netSalary,
            'details' => [
                'basic_salary' => $basic,
                'da' => $da,
                'hra' => $hra,
                'additional_earnings' => $additionalEarnings,
                'manual_allowances' => $manualAllowances,
                'recurring_income' => $recurringIncome,
                'total_income' => $totalIncome,
                'epf' => $epf,
                'medical_deduction' => $medical,
                'additional_deductions' => $additionalDeductions,
                'attendance_deduction' => $attendanceDeduction,
                'manual_deductions' => $manualDeductions,
                'standard_deductions' => $standardDeductions,
                'total_deductions' => $totalDeductions,
                'net_salary' => $netSalary,
                'working_days' => self::WORKING_DAYS,
                'present_days' => $presentDays,
                'absent_days' => $absentDays,
                'leave_days' => $leaveDays,
                'paid_leave_days' => $paidLeaveDays,
                'unpaid_leave_days' => $unpaidLeaveDays,
                'holiday_days' => $holidayDays,
                'daily_rate' => $dailyRate,
                'currency' => strtoupper($structure['currency'] ?? 'INR'),
            ],
        ];
    }

    public function paymentSnapshot(SalaryPayment $payment): array
    {
        if (!$payment->hasComponentSnapshot()) {
            return [
                'legacy' => true,
                'basic_salary' => null,
                'da' => null,
                'hra' => null,
                'additional_earnings' => null,
                'manual_allowances' => (float) ($payment->manual_allowances ?? 0),
                'total_income' => (float) ($payment->total_income ?? $payment->amount),
                'epf' => null,
                'medical_deduction' => null,
                'additional_deductions' => null,
                'attendance_deduction' => null,
                'manual_deductions' => (float) ($payment->manual_deductions ?? 0),
                'total_deductions' => (float) ($payment->total_deductions ?? 0),
                'net_salary' => (float) $payment->amount,
                'currency' => $payment->currency ?: 'INR',
            ];
        }

        $snapshot = ['legacy' => false];
        foreach ([
            'basic_salary', 'da', 'hra', 'additional_earnings', 'manual_allowances',
            'total_income', 'epf', 'medical_deduction', 'additional_deductions',
            'attendance_deduction', 'manual_deductions', 'total_deductions',
            'net_salary', 'daily_rate', 'working_days', 'present_days', 'absent_days',
            'leave_days', 'paid_leave_days', 'unpaid_leave_days', 'holiday_days',
        ] as $field) {
            $snapshot[$field] = $payment->{$field};
        }
        $snapshot['currency'] = $payment->currency ?: 'INR';

        return $snapshot;
    }

    private function money($value): float
    {
        return round((float) $value, 2);
    }
}
