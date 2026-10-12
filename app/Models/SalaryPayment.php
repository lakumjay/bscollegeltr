<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'payment_date',
        'amount',
        'currency',
        'status',
        'month_year',
        'remarks',
        'manual_allowances',
        'manual_deductions',
        'basic_salary',
        'da',
        'hra',
        'additional_earnings',
        'epf',
        'medical_deduction',
        'additional_deductions',
        'attendance_deduction',
        'total_income',
        'total_deductions',
        'net_salary',
        'daily_rate',
        'working_days',
        'present_days',
        'absent_days',
        'leave_days',
        'paid_leave_days',
        'unpaid_leave_days',
        'holiday_days',
        'snapshot_version',
    ];

    protected $dates = ['payment_date'];

    protected $casts = [
        'amount' => 'decimal:2',
        'manual_allowances' => 'decimal:2',
        'manual_deductions' => 'decimal:2',
        'basic_salary' => 'decimal:2',
        'da' => 'decimal:2',
        'hra' => 'decimal:2',
        'additional_earnings' => 'decimal:2',
        'epf' => 'decimal:2',
        'medical_deduction' => 'decimal:2',
        'additional_deductions' => 'decimal:2',
        'attendance_deduction' => 'decimal:2',
        'total_income' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'daily_rate' => 'decimal:2',
        'snapshot_version' => 'integer',
    ];

    public function hasComponentSnapshot(): bool
    {
        return $this->snapshot_version > 0;
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class)->withTrashed();
    }
}
