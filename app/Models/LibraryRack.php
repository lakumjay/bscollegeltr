<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibraryRack extends Model
{
    use HasFactory;
     protected $table="library_racks";
        protected $fillable = [
'name','NumberOfRows'        ];
}
