<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentCourse extends Model
{
    use HasFactory;
    protected $fillable=[
        'user_id',
        'student_id',
        'session_id',
        'semaster'
     ];

     public function session()
     {
         return $this->belongsTo(Session::class,'session_id','id');
     }
}
