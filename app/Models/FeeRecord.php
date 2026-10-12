<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'student_id',
        'course_id',
        'fee_head_id',
        'fee_head_name',
        'special_category_id',
        'amount',
        'discount',
        'net_amount',
        'due_date',
        'notes',
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

    public function feeStructure()
    {
        return $this->belongsTo(FeeStructure::class, 'fee_head_id', 'fee_head_id');
    }

    public function specialCategory()
    {
        return $this->belongsTo(SpecialCategory::class);
    }
}