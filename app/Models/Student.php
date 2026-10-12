<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = ['reg_no',
        'roll_no', 'first_name', 'last_name', 'gender', 'date_of_birth', 'phone_number',
        'email', 'father_name', 'mother_name', 'course', 'batch', 'admission_date',
        'current_year', 'current_semester', 'status', 'session_id', 'subjects', 'photo',
        'aadhar_card', 'id_proof', 'category', 'caste', 'category_certificate', 'user_id'
    ,
    'correspondence_address',
'permanent_address',
'student_id',
    ];

    protected $casts = [
        'subjects' => 'array',
        'date_of_birth' => 'date',
        'admission_date' => 'date',
    ];

    public function session()
    {
        return $this->belongsTo(Session::class);
    }
    public function StudentCourse()
    {
        return $this->hasMany(StudentCourse::class);
    }
    public function course_details()
    {
        return $this->belongsTo(Courses::class,'course','id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function certificates()
    {
        return $this->hasMany(StudentCertificate::class, 'student_id');
    }
}
?>