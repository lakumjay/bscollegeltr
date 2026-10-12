<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialCategoryFeeHead extends Model
{
    use HasFactory;

    protected $fillable = [
        'special_category_id',
        'fee_head_id',
        'discount_type',
        'discount',
    ];

    public function specialCategory()
    {
        return $this->belongsTo(SpecialCategory::class, 'special_category_id');
    }

    public function feeStructure()
    {
        return $this->belongsTo(FeeStructure::class, 'fee_head_id', 'fee_head_id');
    }
}