<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\LibraryCategory;
use DB;
class LibraryCategoryController extends Controller
{

     public function __construct()
    {
        $this->middleware('auth');
       
    }
public function index()
{
  // $uid=Auth::user()->id;
 
   $uid=Auth::user()->id; 
  
 $editor=1;
 
 
// .................
$user_id=Auth::user()->id;
 
$session_id=Auth::user()->active_session_id;


 
	$LibraryCategory=LibraryCategory::get();
	 return view('admin/library/categories',compact('LibraryCategory'));
}
public function edit($id)
{
   $uid=Auth::user()->id;

	$CategoryEdit=LibraryCategory::where('id',$id)->get();
	if(count($CategoryEdit)>0){
		foreach($CategoryEdit as $data){
	$LibraryCategory=LibraryCategory::get();
$edit=true;
	 return view('admin/library/categories',compact('edit','data','LibraryCategory'));
	}

		}
		   return redirect("admin/library/categories");

}
public function editStore($id,Request $req)
{
   $uid=Auth::user()->id;

	$CategoryEdit=LibraryCategory::where('id',$id)->get();
	if(count($CategoryEdit)>0){
	 LibraryCategory::where('id',$id)->update(['name'=>$req->name]);
   return redirect("admin/library/categories")->with("success","Category updated successfully!");

		}
		   return redirect("admin/library/categories");

}
   public function store(Request $req)
   {
   $uid=Auth::user()->id;
   $Category=new LibraryCategory();
   $Category->name=$req->name;
    $Category->uid=$uid;
   $Category->save();
   return redirect("admin/library/categories")->with("success","Category created successfully!");
   }
}
