<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradingScale extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'name',
        'scale',
    ];

    protected $casts = [
        'scale' => 'array', // JSON: [{min: 90, max: 100, grade: 'A+'}, ...]
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }
}