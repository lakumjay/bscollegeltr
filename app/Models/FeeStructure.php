<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeStructure extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'course_id',
        'fee_head_id',
        'fee_head_name',
        'amount',
        'is_optional'
    ];

    public function course()
    {
        return $this->belongsTo(Courses::class, 'course_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function session()
    {
        return $this->belongsTo(Session::class, 'session_id');
    }
}