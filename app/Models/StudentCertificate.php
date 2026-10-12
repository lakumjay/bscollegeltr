<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentCertificate extends Model
{
    protected $table="student_certificate";
    protected $fillable = [
         'type',
         'data',
         'sr',
         'certificate_number',
         'student_id',
         'generated_by'
    ];

    
}
?>
