<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id', 'user_name', 'action', 'module', 'description',
        'url', 'method', 'ip_address', 'request_data',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
