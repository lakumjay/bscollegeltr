<?php

namespace Tests\Feature;

use App\Models\Courses;
use App\Models\FeePayment;
use App\Models\Session;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class FeeReceiptViewTest extends TestCase
{
    public function test_full_receipt_renders_without_a_profile_or_banner(): void
    {
        $user = new User(['name' => 'Fee Admin']);
        $user->setRelation('session', new Session(['session_name' => '2026-2027']));
        Auth::setUser($user);

        $student = new Student([
            'first_name' => 'Test',
            'last_name' => 'Student',
            'roll_no' => '12',
        ]);
        $course = new Courses(['name' => 'Science']);
        $payment = new FeePayment([
            'amount' => 500,
            'payment_date' => Carbon::parse('2026-09-01'),
            'payment_method' => 'Cash',
        ]);
        $payment->id = 1;

        $html = view('admin.fee.fee_receipt', [
            'student' => $student,
            'course' => $course,
            'feeRecords' => collect(),
            'payments' => collect([$payment]),
            'total_fees' => 1000,
            'profile' => null,
        ])->render();

        $this->assertStringContainsString('FEE RECEIPT', $html);
        $this->assertStringContainsString('Due Amount:', $html);
        $this->assertStringContainsString('College', $html);
    }
}
