<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'college_name', 'name_in_hindi', 'contact_number', 'email', 'address', 'website', 'lc_prefix', 'logo','banner'];
}
