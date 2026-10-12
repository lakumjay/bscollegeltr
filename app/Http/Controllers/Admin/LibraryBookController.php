<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\LibraryBook;
use App\Models\LibraryCategory;
use App\Models\LibraryAuthor;
use App\Models\LibraryPublisher;
use App\Models\LibraryRack;
use DB;
class LibraryBookController extends Controller
{

     public function __construct()
    {
        $this->middleware('auth');
       
    }
    public function edit($id)
    {
      $uid=Auth::user()->id;
	 $session_id=Auth::user()->active_session_id;

 
// .................
$user_id=Auth::user()->id;

 
  $LibraryBookEdit=LibraryBook::where('id',$id)->get();
  if(count($LibraryBookEdit)>0){
    foreach($LibraryBookEdit as $data){

  $LibraryCategory=LibraryCategory::get();
  $LibraryAuthor=LibraryAuthor::get();
  $LibraryPublisher=LibraryPublisher::get();
  $LibraryBook=LibraryBook::get();
    $LibraryRack=LibraryRack::get();

   return view('admin/library/book_edit',compact('LibraryBook','LibraryCategory','LibraryAuthor','LibraryPublisher','LibraryRack','data'));
    }}
    }






        public function editSave($id,Request $req)
    {
 	 
// .................
$user_id=Auth::user()->id;
$uid=Auth::user()->id;
  
 
  $LibraryBookEdit=LibraryBook::where('id',$id)->get();
  if(count($LibraryBookEdit)>0){
   LibraryBook::where('id',$id)->update([
'AccessionNumber'=>$req->AccessionNumber,
'ClassificationNumber'=>$req->ClassificationNumber,
'BookTitle'=>$req->BookTitle,
'Langauge'=>$req->Langauge,
'ISBN_No'=>$req->ISBN_No,
'VolumeNo'=>$req->VolumeNo,
'Page'=>$req->Page,
'RowNumber'=>$req->RowNumber,
'Price'=>$req->Price,
'Status'=>$req->Status,
'library_categories_id'=>$req->library_categories_id,
'library_authors_id'=>$req->library_authors_id,
'library_publishers_id'=>$req->library_publishers_id,
'library_racks_id'=>$req->library_racks_id
]);
 return redirect("admin/library/book/edit/".$id)->with("success","Book created successfully!");

     }
    }
public function index()
{
  	 
   $uid=Auth::user()->id; 
$session_id=Auth::user()->active_session_id;
 
// .................
$user_id=Auth::user()->uid;
  
  $LibraryBook=LibraryBook::with("LibraryCategory")->with("LibraryAuthor")->with("LibraryPublisher")->with("LibraryRack")  ->get();
 
   return view('admin/library/book',compact('LibraryBook'));
}
public function add()
{
 
   $uid=Auth::user()->id; 
 $type=Auth::user()->type;
 
 
// .................
$user_id=Auth::user()->id;
$session_id=Auth::user()->active_session_id;
 
  $LibraryCategory=LibraryCategory::get();
  $LibraryAuthor=LibraryAuthor::get();
  $LibraryPublisher=LibraryPublisher::get();
  $LibraryBook=LibraryBook::get();
    $LibraryRack=LibraryRack::get();

   return view('admin/library/book_add',compact('LibraryBook','LibraryCategory','LibraryAuthor','LibraryPublisher','LibraryRack'));
}
public function store(Request $req)
{
  	 
   $uid=Auth::user()->id; 
  
 
// .................
$user_id=Auth::user()->id;

$session_id=Auth::user()->active_session_id;

 
   $LibraryBook=new LibraryBook();
$LibraryBook->AccessionNumber=$req->AccessionNumber;
$LibraryBook->ClassificationNumber=$req->ClassificationNumber;
$LibraryBook->BookTitle=$req->BookTitle;
$LibraryBook->Langauge=$req->Langauge;
$LibraryBook->ISBN_No=$req->ISBN_No;
$LibraryBook->VolumeNo=$req->VolumeNo;
$LibraryBook->Page=$req->Page;
$LibraryBook->RowNumber=$req->RowNumber;
$LibraryBook->Price=$req->Price;
$LibraryBook->Status=$req->Status;
$LibraryBook->library_categories_id=$req->library_categories_id;
$LibraryBook->library_authors_id=$req->library_authors_id;
$LibraryBook->library_publishers_id=$req->library_publishers_id;
$LibraryBook->library_racks_id=$req->library_racks_id;
$LibraryBook->uid=$uid;
$LibraryBook->user_id=$user_id;
$LibraryBook->save();
return redirect("admin/library/book_add")->with("success","Book created successfully!");
}
	
public function issue_book (){
	
	return view('admin/library/issue_book');
	
}
	
}
