<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'college_name', 'name_in_hindi', 'university_name', 'affiliation',
        'contact_number', 'email', 'address', 'website', 'lc_prefix', 'auto_cert_number', 'logo', 'banner'
    ];

    protected $casts = [
        'auto_cert_number' => 'boolean',
    ];
}
