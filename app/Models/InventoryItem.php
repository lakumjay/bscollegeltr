<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $fillable = [
        'name',
        'sku',
        'bill_no',
        'purchase_date',
        'party_name',
        'supplier_name',
        'supplier_address',
        'payment_method',
        'cheque_no',
        'transaction_id',
        'description',
        'unit_price',
        'user_id',
    ];

    protected $casts = [
        'purchase_date' => 'date',
    ];

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}