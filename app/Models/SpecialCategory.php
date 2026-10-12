<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'category_name',
    ];

    public function course()
    {
        return $this->belongsTo(Courses::class, 'course_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function feeHeads()
    {
        return $this->hasMany(SpecialCategoryFeeHead::class, 'special_category_id');
    }
}