<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryStructure extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'basic_pay',
        'da',
        'hra',
        'additional_earnings',
        'epf',
        'medical_deduction',
        'additional_deductions',
        'allowances',
        'deductions',
        'currency',
        'notes',
    ];

    protected $casts = [
        'basic_pay' => 'decimal:2',
        'da' => 'decimal:2',
        'hra' => 'decimal:2',
        'additional_earnings' => 'decimal:2',
        'epf' => 'decimal:2',
        'medical_deduction' => 'decimal:2',
        'additional_deductions' => 'decimal:2',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class)->withTrashed();
    }
}
