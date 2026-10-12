<?php

namespace Tests\Unit;

use App\Services\PayrollCalculator;
use PHPUnit\Framework\TestCase;

class PayrollCalculatorTest extends TestCase
{
    public function test_it_calculates_all_income_and_deduction_components(): void
    {
        $result = (new PayrollCalculator())->calculateComponents([
            'basic_salary' => 22000,
            'da' => 2200,
            'hra' => 4400,
            'additional_earnings' => 1000,
            'epf' => 1800,
            'medical_deduction' => 200,
            'additional_deductions' => 300,
            'currency' => 'inr',
        ], [], 500, 400);

        $this->assertSame(29600.0, $result['details']['recurring_income']);
        $this->assertSame(30100.0, $result['details']['total_income']);
        $this->assertSame(2300.0, $result['details']['standard_deductions']);
        $this->assertSame(2700.0, $result['details']['total_deductions']);
        $this->assertSame(27400.0, $result['details']['net_salary']);
        $this->assertSame('INR', $result['details']['currency']);
    }

    public function test_attendance_deduction_keeps_two_leaves_paid(): void
    {
        $result = (new PayrollCalculator())->calculateComponents([
            'basic_salary' => 22000,
        ], [
            'present_days' => 15,
            'absent_days' => 1,
            'leave_days' => 3,
            'holiday_days' => 2,
        ]);

        $this->assertSame(2, $result['details']['paid_leave_days']);
        $this->assertSame(1, $result['details']['unpaid_leave_days']);
        $this->assertSame(1000.0, $result['details']['daily_rate']);
        $this->assertSame(2000.0, $result['details']['attendance_deduction']);
        $this->assertSame(20000.0, $result['amount']);
    }

    public function test_manual_adjustments_are_kept_separate_from_recurring_components(): void
    {
        $result = (new PayrollCalculator())->calculateComponents([
            'basic_salary' => 10000,
            'additional_earnings' => 1000,
            'additional_deductions' => 500,
        ], [], 750, 250);

        $this->assertSame(11000.0, $result['details']['recurring_income']);
        $this->assertSame(750.0, $result['details']['manual_allowances']);
        $this->assertSame(250.0, $result['details']['manual_deductions']);
        $this->assertSame(11000.0, $result['amount']);
    }
}
