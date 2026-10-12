<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\LibraryAuthor;
use DB; 
class LibraryAuthorController extends Controller
{

     public function __construct()
    {
        $this->middleware('auth');
       
    }
public function index()
{
  // $uid=Auth::user()->id;
 
   $uid=Auth::user()->id; 
 
// .................
$user_id=Auth::user()->id;
$session_id=Auth::user()->active_session_id;

 
	$LibraryAuthor=LibraryAuthor::get();
	 return view('admin/library/author',compact('LibraryAuthor'));
}
public function edit($id)
{
   //$uid=Auth::user()->id;
	 
   $uid=Auth::user()->id; 
  
// .................
$user_id=Auth::user()->id;
$session_id=Auth::user()->active_session_id;

 
	$CategoryEdit=LibraryAuthor::where('id',$id)->get();
	if(count($CategoryEdit)>0){
		foreach($CategoryEdit as $data){
	$LibraryAuthor=LibraryAuthor::get();
$edit=true;
	 return view('admin/library/author',compact('edit','data','LibraryAuthor'));
	}

		}
		   return redirect("admin/library/author");

}
public function editStore($id,Request $req)
{
  
   $uid=Auth::user()->id; 
 
// .................
$user_id=Auth::user()->id;
$session_id=Auth::user()->active_session_id;

 
	$CategoryEdit=LibraryAuthor::where('id',$id)->get();
	if(count($CategoryEdit)>0){
	 LibraryAuthor::where('id',$id)->update(['name'=>$req->name]);
   return redirect("admin/library/author")->with("success","author updated successfully!");

		}
		   return redirect("admin/library/author");

}
   public function store(Request $req)
   {
   $uid=Auth::user()->id;
   $Category=new LibraryAuthor();
   $Category->name=$req->name;
    $Category->uid=$uid;
   $Category->save();
   return redirect("admin/library/author")->with("success","Category created successfully!");
   }
}
