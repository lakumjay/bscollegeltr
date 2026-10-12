<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\SalaryPayment;
use App\Models\SalaryStructure;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class StaffPayrollTest extends TestCase
{
    use DatabaseTransactions;

    public function test_salary_structure_navigation_is_centralized_on_the_payment_page(): void
    {
        [$admin, $staff] = $this->payrollFixture();

        $this->actingAs($admin)
            ->get(route('admin.staff.index'))
            ->assertOk()
            ->assertDontSee('Salary structure');

        $this->actingAs($admin)
            ->get(route('admin.staff.salary.payments', $staff))
            ->assertOk()
            ->assertDontSee('Salary structure')
            ->assertDontSee('Recurring income')
            ->assertSee('name="basic_pay"', false)
            ->assertSee('name="da"', false)
            ->assertSee('name="hra"', false)
            ->assertSee('name="additional_earnings"', false)
            ->assertSee('name="epf"', false)
            ->assertSee('name="medical_deduction"', false)
            ->assertSee('name="additional_deductions"', false)
            ->assertDontSee('name="manual_allowances"', false)
            ->assertDontSee('name="manual_deductions"', false)
            ->assertDontSee('name="currency"', false)
            ->assertSee('Complete salary breakdown')
            ->assertSee('Salary Payment')
            ->assertSee('Basic Salary')
            ->assertSee('DA')
            ->assertSee('HRA')
            ->assertSee('Additional Pay')
            ->assertSee('Income')
            ->assertSee('EPF')
            ->assertSee('Medical Deduction')
            ->assertSee('Additional Deductions')
            ->assertSee('Attendance Deduction')
            ->assertSee('Net Payment')
            ->assertDontSee('Manual Additional Earnings')
            ->assertDontSee('Manual Additional Deductions')
            ->assertDontSee('Calculated net payment')
            ->assertDontSee('New salary payment')
            ->assertSee('Total Income')
            ->assertSee('Total Deductions')
            ->assertSee('Net Salary')
            ->assertSee('Save salary details');
    }

    public function test_salary_structure_components_are_saved_as_the_canonical_source(): void
    {
        $admin = $this->admin();
        $staff = $this->staff();

        $this->actingAs($admin)->post(route('admin.staff.salary.structure', $staff), [
            'basic_pay' => 15000,
            'da' => 1500,
            'hra' => 3000,
            'additional_earnings' => 750,
            'epf' => 1200,
            'medical_deduction' => 150,
            'additional_deductions' => 100,
            'currency' => 'usd',
            'notes' => 'Revised structure',
            'salary' => 999999,
        ])->assertRedirect(route('admin.staff.salary.structure', $staff));

        $structure = SalaryStructure::where('staff_id', $staff->id)->firstOrFail();
        $this->assertSame(750.0, (float) $structure->additional_earnings);
        $this->assertSame(750.0, (float) $structure->allowances);
        $this->assertSame(100.0, (float) $structure->additional_deductions);
        $this->assertSame(100.0, (float) $structure->deductions);
        $this->assertSame('USD', $structure->currency);
        $this->assertNull($staff->fresh()->salary);
    }

    public function test_submitted_totals_are_ignored_and_component_snapshot_is_saved(): void
    {
        [$admin, $staff] = $this->payrollFixture();

        $response = $this->actingAs($admin)->post(route('admin.staff.salary.payments'), array_merge(
            $this->paymentPayload($staff, '2031-01'), [
            'payment_date' => '2031-01-31',
            'amount' => 999999,
            'total_income' => 999999,
            'total_deductions' => 0,
            'currency' => 'USD',
        ]));

        $response->assertRedirect(route('admin.staff.salary.payments'));
        $payment = SalaryPayment::where('staff_id', $staff->id)->where('month_year', '2031-01')->firstOrFail();

        $this->assertSame(29600.0, (float) $payment->total_income);
        $this->assertSame(2300.0, (float) $payment->total_deductions);
        $this->assertSame(27300.0, (float) $payment->net_salary);
        $this->assertSame(27300.0, (float) $payment->amount);
        $this->assertSame(0.0, (float) $payment->manual_allowances);
        $this->assertSame(0.0, (float) $payment->manual_deductions);
        $this->assertSame('INR', $payment->currency);
        $this->assertSame(1, $payment->snapshot_version);
    }

    public function test_duplicate_monthly_payment_returns_clear_validation_error(): void
    {
        [$admin, $staff] = $this->payrollFixture();
        $this->recordPayment($admin, $staff, '2031-02');

        $response = $this->actingAs($admin)
            ->from(route('admin.staff.salary.payments'))
            ->post(route('admin.staff.salary.payments'), $this->paymentPayload($staff, '2031-02'));

        $response->assertRedirect(route('admin.staff.salary.payments'))
            ->assertSessionHasErrors(['month_year']);
        $this->assertSame(1, SalaryPayment::where('staff_id', $staff->id)->where('month_year', '2031-02')->count());
    }

    public function test_payment_page_creates_structure_and_rejects_negative_values(): void
    {
        $admin = $this->admin();
        $staff = $this->staff();

        $this->actingAs($admin)
            ->from(route('admin.staff.salary.payments'))
            ->post(route('admin.staff.salary.payments'), [
                'staff_id' => $staff->id,
                'payment_date' => '2031-03-28',
                'month_year' => '2031-03',
                'status' => 'Paid',
                'currency' => 'INR',
            ])
            ->assertSessionHasErrors(['basic_pay', 'da', 'hra', 'additional_earnings', 'epf', 'medical_deduction', 'additional_deductions']);

        $this->actingAs($admin)
            ->post(route('admin.staff.salary.payments'), $this->paymentPayload($staff, '2031-03'))
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('salary_structures', ['staff_id' => $staff->id, 'basic_pay' => 22000]);

        $this->actingAs($admin)
            ->from(route('admin.staff.salary.payments'))
            ->post(route('admin.staff.salary.payments'), array_merge($this->paymentPayload($staff, '2031-04'), [
                'manual_allowances' => -1,
                'manual_deductions' => -2,
                'epf' => -100,
            ]))
            ->assertSessionHasErrors(['epf']);
    }

    public function test_salary_details_can_be_saved_from_payment_page_without_recording_payment(): void
    {
        $admin = $this->admin();
        $staff = $this->staff();

        $this->actingAs($admin)
            ->post(route('admin.staff.salary.payments'), array_merge($this->paymentPayload($staff, '2031-03'), [
                'submit_action' => 'save_structure',
                'basic_pay' => 25000,
                'currency' => 'usd',
            ]))
            ->assertRedirect(route('admin.staff.salary.payments', $staff->id))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('salary_structures', [
            'staff_id' => $staff->id,
            'basic_pay' => 25000,
            'currency' => 'INR',
        ]);
        $this->assertSame(0, SalaryPayment::where('staff_id', $staff->id)->count());
    }

    public function test_live_calculation_uses_the_editable_payment_fields(): void
    {
        $admin = $this->admin();
        $staff = $this->staff();

        $this->actingAs($admin)
            ->postJson(route('admin.staff.salary.calculate'), [
                'staff_id' => $staff->id,
                'month_year' => '2031-03',
                'basic_pay' => 10000,
                'da' => 1000,
                'hra' => 2000,
                'additional_earnings' => 500,
                'manual_allowances' => 250,
                'epf' => 800,
                'medical_deduction' => 100,
                'additional_deductions' => 200,
                'manual_deductions' => 50,
                'currency' => 'usd',
            ])
            ->assertOk()
            ->assertJsonPath('details.total_income', 13500)
            ->assertJsonPath('details.total_deductions', 1100)
            ->assertJsonPath('details.net_salary', 12400)
            ->assertJsonPath('details.manual_allowances', 0)
            ->assertJsonPath('details.manual_deductions', 0)
            ->assertJsonPath('details.currency', 'INR');
    }

    public function test_attendance_and_leave_values_are_saved_in_the_payment_snapshot(): void
    {
        [$admin, $staff] = $this->payrollFixture();
        foreach ([
            '2031-04-01' => 'Absent',
            '2031-04-02' => 'Leave',
            '2031-04-03' => 'Leave',
            '2031-04-04' => 'Leave',
            '2031-04-05' => 'Present',
        ] as $date => $status) {
            Attendance::create(compact('date', 'status') + ['staff_id' => $staff->id]);
        }

        $this->recordPayment($admin, $staff, '2031-04');
        $payment = SalaryPayment::where('staff_id', $staff->id)->where('month_year', '2031-04')->firstOrFail();

        $this->assertSame(2, $payment->paid_leave_days);
        $this->assertSame(1, $payment->unpaid_leave_days);
        $this->assertSame(2000.0, (float) $payment->attendance_deduction);
        $this->assertSame(25300.0, (float) $payment->net_salary);
    }

    public function test_historical_payslip_is_stable_after_structure_and_attendance_changes(): void
    {
        [$admin, $staff, $structure] = $this->payrollFixture();
        $this->recordPayment($admin, $staff, '2031-05');

        $structure->update(['basic_pay' => 99999, 'da' => 9999, 'additional_earnings' => 9999]);
        Attendance::create(['staff_id' => $staff->id, 'date' => '2031-05-01', 'status' => 'Absent']);

        $this->actingAs($admin)
            ->get(route('admin.staff.salary.payslip', [$staff, '2031-05']))
            ->assertOk()
            ->assertSee('Basic Salary')
            ->assertSee('Dearness Allowance (DA)')
            ->assertSee('House Rent Allowance (HRA)')
            ->assertSee('Additional Pay')
            ->assertSee('Income Component')
            ->assertSee('Total Gross Payment')
            ->assertSee('Net Take Salary')
            ->assertSee('Employee Provident Fund (EPF)')
            ->assertSee('Medical Deduction')
            ->assertSee('22,000.00')
            ->assertSee('27,300.00')
            ->assertDontSee('Additional Earnings')
            ->assertDontSee('Total Gross Earnings')
            ->assertDontSee('Net Take-Home Salary')
            ->assertDontSee('Leaves:')
            ->assertDontSee('Holidays:')
            ->assertDontSee('Daily Rate:')
            ->assertDontSee('99,999.00');
    }

    public function test_report_and_csv_use_saved_component_totals(): void
    {
        [$admin, $staff] = $this->payrollFixture();
        $this->recordPayment($admin, $staff, '2031-06');
        $query = ['start_date' => '2031-06-01', 'end_date' => '2031-06-30', 'staff_id' => $staff->id];

        $this->actingAs($admin)
            ->get(route('admin.staff.salary.report', $query))
            ->assertOk()
            ->assertSee('29,600.00')
            ->assertSee('2,300.00')
            ->assertSee('27,300.00');

        $response = $this->actingAs($admin)->get(route('admin.staff.salary.export_csv', $query));
        $response->assertOk();
        ob_start();
        $response->baseResponse->sendContent();
        $csv = ob_get_clean();

        $this->assertStringContainsString('Basic Salary', $csv);
        $this->assertStringContainsString('Additional Pay', $csv);
        $this->assertStringContainsString('29600', $csv);
        $this->assertStringContainsString('2300', $csv);
        $this->assertStringContainsString('27300', $csv);
        $this->assertStringContainsString('Component snapshot', $csv);
    }

    public function test_legacy_payment_remains_viewable_without_a_current_structure(): void
    {
        $admin = $this->admin();
        $staff = $this->staff();
        SalaryPayment::create([
            'staff_id' => $staff->id,
            'payment_date' => '2030-12-31',
            'amount' => 1234.56,
            'net_salary' => 1234.56,
            'total_income' => 1284.56,
            'total_deductions' => 50,
            'manual_allowances' => 75,
            'manual_deductions' => 50,
            'currency' => 'INR',
            'status' => 'Paid',
            'month_year' => '2030-12',
            'snapshot_version' => 0,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.staff.salary.payslip', [$staff, '2030-12']))
            ->assertOk()
            ->assertSee('Legacy consolidated payment')
            ->assertSee('Basic Salary')
            ->assertSee('House Rent Allowance (HRA)')
            ->assertSee('Not stored')
            ->assertSee('1,234.56');
    }

    public function test_payroll_migration_columns_and_backfill_invariants_are_present(): void
    {
        foreach (['da', 'hra', 'additional_earnings', 'epf', 'medical_deduction', 'additional_deductions'] as $column) {
            $this->assertTrue(Schema::hasColumn('salary_structures', $column));
        }
        foreach (['manual_allowances', 'net_salary', 'total_income', 'total_deductions', 'snapshot_version'] as $column) {
            $this->assertTrue(Schema::hasColumn('salary_payments', $column));
        }

        $this->assertSame(0, DB::table('salary_structures')
            ->whereColumn('additional_earnings', '<>', 'allowances')
            ->orWhereColumn('additional_deductions', '<>', 'deductions')
            ->count());
        if (Schema::hasColumn('salary_payments', 'manual_allounces')) {
            $this->assertSame(0, DB::table('salary_payments')
                ->whereRaw('COALESCE(manual_allowances, 0) <> COALESCE(manual_allounces, 0)')
                ->count());
        }
    }

    private function payrollFixture(): array
    {
        $admin = $this->admin();
        $staff = $this->staff();
        $structure = SalaryStructure::create($this->structurePayload($staff));

        return [$admin, $staff, $structure];
    }

    private function admin(): User
    {
        return User::create([
            'name' => 'Payroll Test Admin',
            'email' => uniqid('payroll-admin-', true) . '@example.test',
            'password' => Hash::make('password'),
            'type' => 'admin',
        ]);
    }

    private function staff(): Staff
    {
        $key = uniqid('payroll-', false);

        return Staff::create([
            'staff_id' => $key,
            'first_name' => 'Payroll',
            'last_name' => 'Tester',
            'email' => $key . '@example.test',
            'role' => 'Accountant',
            'status' => 'Active',
        ]);
    }

    private function structurePayload(Staff $staff): array
    {
        return [
            'staff_id' => $staff->id,
            'basic_pay' => 22000,
            'da' => 2200,
            'hra' => 4400,
            'additional_earnings' => 1000,
            'epf' => 1800,
            'medical_deduction' => 200,
            'additional_deductions' => 300,
            'allowances' => 1000,
            'deductions' => 300,
            'currency' => 'INR',
        ];
    }

    private function paymentPayload(Staff $staff, string $month): array
    {
        return [
            'staff_id' => $staff->id,
            'payment_date' => $month . '-28',
            'month_year' => $month,
            'status' => 'Paid',
            'basic_pay' => 22000,
            'da' => 2200,
            'hra' => 4400,
            'additional_earnings' => 1000,
            'epf' => 1800,
            'medical_deduction' => 200,
            'additional_deductions' => 300,
        ];
    }

    private function recordPayment(User $admin, Staff $staff, string $month): void
    {
        $this->actingAs($admin)
            ->post(route('admin.staff.salary.payments'), $this->paymentPayload($staff, $month))
            ->assertRedirect(route('admin.staff.salary.payments'))
            ->assertSessionHasNoErrors();
    }
}
