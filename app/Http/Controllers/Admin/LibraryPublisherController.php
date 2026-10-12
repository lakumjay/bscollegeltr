<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\LibraryPublisher;
use DB;
class LibraryPublisherController extends Controller
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

 
	$LibraryPublisher=LibraryPublisher::get();
	 return view('admin/library/publisher',compact('LibraryPublisher'));
}
public function edit($id)
{
//   $uid=Auth::user()->id;
	 
   $uid=Auth::user()->id; 
  
// .................
$user_id=Auth::user()->id;
$session_id=Auth::user()->active_session_id;

 
	$CategoryEdit=LibraryPublisher::where('id',$id)->get();
	if(count($CategoryEdit)>0){
		foreach($CategoryEdit as $data){
	$LibraryPublisher=LibraryPublisher::get();
$edit=true;
	 return view('admin/library/publisher',compact('edit','data','LibraryPublisher'));
	}

		}
		   return redirect("admin/library/publisher");

}
public function editStore($id,Request $req)
{
 //  $uid=Auth::user()->id;
	 
   $uid=Auth::user()->id; 
  
// .................
$user_id=Auth::user()->id;
$session_id=Auth::user()->active_session_id;

 
	$CategoryEdit=LibraryPublisher::where('id',$id)->get();
	if(count($CategoryEdit)>0){
	 LibraryPublisher::where('id',$id)->update(['name'=>$req->name]);
   return redirect("admin/library/publisher")->with("success","Publisher updated successfully!");

		}
		   return redirect("admin/library/publisher");

}
   public function store(Request $req)
   {
 //  $uid=Auth::user()->id;
 	  
   $uid=Auth::user()->id; 
  
// .................
$user_id=Auth::user()->id;
$session_id=Auth::user()->active_session_id;

 
   $Category=new LibraryPublisher();
   $Category->name=$req->name;
    $Category->uid=$uid;
   $Category->save();
   return redirect("admin/library/publisher")->with("success","Category created successfully!");
   }
}
