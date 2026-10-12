<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Courses extends Model
{
    use HasFactory;
    protected $fillable=['user_id','name','sr'];
    public function subjects()
    {
        return $this->hasMany(Subject::class, 'course_id');
    }
}
