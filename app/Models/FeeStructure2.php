<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeStructure2 extends Model
{
    use HasFactory;
protected $table="fee_structure2";
    protected $fillable = [
        'head_name',
        'sem1',
        'sem2',
        'sem3',
        'sem4',
        'sem5',
        'sem6',
        'sem6',
        'sem7',
        'sem8',
        'course_id',
        'session_id'
    ];

  
}