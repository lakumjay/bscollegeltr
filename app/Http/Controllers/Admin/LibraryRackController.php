<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\LibraryRack;
use DB;
class LibraryRackController extends Controller
{

     public function __construct()
    {
        $this->middleware('auth');
       
    }
public function index()
{
   
   $uid=Auth::user()->id; 
  
// .................
$user_id=Auth::user()->id;
 $session_id=Auth::user()->active_session_id;

 
 //$uid=Auth::user()->id;

	$LibraryRack=LibraryRack::get();
	 return view('admin/library/rack',compact('LibraryRack'));
}
public function edit($id)
{
 //  $uid=Auth::user()->id;
	 
   $uid=Auth::user()->id; 
  
 
// .................
$user_id=Auth::user()->id;
 $session_id=Auth::user()->active_session_id;

	$CategoryEdit=LibraryRack::where('id',$id)->get();
	if(count($CategoryEdit)>0){
		foreach($CategoryEdit as $data){
	$LibraryRack=LibraryRack::get();
$edit=true;
	 return view('admin/library/rack',compact('edit','data','LibraryRack'));
	}

		}
		   return redirect("admin/library/rack");

}
public function editStore($id,Request $req)
{
  // $uid=Auth::user()->id;
	 
   $uid=Auth::user()->id; 
  
// .................
$user_id=Auth::user()->id;
$session_id=Auth::user()->active_session_id;

 
	$CategoryEdit=LibraryRack::where('id',$id)->get();
	if(count($CategoryEdit)>0){
	 LibraryRack::where('id',$id)->update(['name'=>$req->name,'NumberOfRows'=>$req->NumberOfRows]);
   return redirect("admin/library/rack")->with("success","Rack updated successfully!");

		}
		   return redirect("admin/library/rack");

}
   public function store(Request $req)
   {
  // $uid=Auth::user()->id;
  		 
   $uid=Auth::user()->id; 
  
// .................
$user_id=Auth::user()->id;
$session_id=Auth::user()->active_session_id;

 
   $Category=new LibraryRack();
   $Category->name=$req->name;
   $Category->NumberOfRows=$req->NumberOfRows;
    $Category->uid=$uid;
   $Category->save();
   return redirect("admin/library/rack")->with("success","Rack created successfully!");
   }
}
