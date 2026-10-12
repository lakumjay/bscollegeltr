<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'staff_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'date_of_birth',
        'gender',
        'address',
        'role',
        'salary',
        'currency',
        'joining_date',
        'qualification',
        'photo',
        'id_proof',
        'certificate',
        'status',
    ];

    protected $dates = ['date_of_birth', 'joining_date'];

    public function salaryStructure()
    {
        return $this->hasOne(SalaryStructure::class);
    }

    public function salaryPayments()
    {
        return $this->hasMany(SalaryPayment::class);
    }
}
