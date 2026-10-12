<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'course_id',
        'name',
        'grading_scale_id',
        'max_marks',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function course()
    {
        return $this->belongsTo(Courses::class);
    }

    public function gradingScale()
    {
        return $this->belongsTo(GradingScale::class);
    }

    public function marks()
    {
        return $this->hasMany(Mark::class);
    }
}