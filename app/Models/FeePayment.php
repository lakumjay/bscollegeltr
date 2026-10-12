<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'student_id',
        'course_id',
        'fee_record_id',
        'amount',
        'discount_eligible',
        'discount_amount',
        'student_type',
        'other_college_name',
        'payment_date',
        'payment_method',
        'transaction_id',
        'notes',
    ];

    protected $casts = [
        'payment_date'      => 'date',
        'discount_eligible' => 'boolean',
        'discount_amount'   => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function course()
    {
        return $this->belongsTo(Courses::class);
    }

    public function feeRecord()
    {
        return $this->belongsTo(FeeRecord::class);
    }
}
