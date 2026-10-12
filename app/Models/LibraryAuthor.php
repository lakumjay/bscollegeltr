<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibraryAuthor extends Model
{
    use HasFactory;
    protected $table="library_authors";
        protected $fillable = [
'name'        ];
}
