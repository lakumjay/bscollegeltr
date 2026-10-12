<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibraryPublisher extends Model
{
    use HasFactory;
    protected $table="library_publishers";
        protected $fillable = [
'name'        ];
}
