<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibraryBook extends Model
{
    use HasFactory;
       protected $table="library_books";
        protected $fillable = [
'AccessionNumber',
'ClassificationNumber',
'BookTitle',
'Langauge',
'ISBN_No',
'VolumeNo',
'Page',
'RowNumber',
'Price',
'Status',
'library_categories_id',
'library_authors_id',
'library_publishers_id',
'library_racks_id',
        ];
               public function LibraryCategory()
{
	return $this->belongsTo(LibraryCategory::class,'library_categories_id','id');
}

         public function LibraryAuthor()
{
	return $this->belongsTo(LibraryAuthor::class,'library_authors_id','id');
}

         public function LibraryPublisher()
{
	return $this->belongsTo(LibraryPublisher::class,'library_publishers_id','id');
}
         public function LibraryRack()
{
	return $this->belongsTo(LibraryCategory::class,'library_racks_id','id');
}
}
