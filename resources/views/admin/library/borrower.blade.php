@extends('layouts.appadmin')

@section('title')
Library::Category Master
@endsection
@section('content')
<?php 
$uid=Auth::user()->id;
?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<div class="container">
    <div class="row justify-content-center">
   
 
 
 
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">{{ __('Add Borrower') }}


                </div>
<?php 
				
				if(isset($_POST['save_borrower'])){
$borrower_id=$_POST['borrower_id'];
$usertype=$_POST['usertype'];
$students_id=$_POST['students_id'];
					$data=array('borrower_id'=>$borrower_id,'usertype'=>$usertype,'students_id'=>$students_id,'uid'=>$uid);
					$ins=DB::table("borrower")->insert($data);
					if($ins>0){
					?>
				<div class="alert alert-success"><b>Success!</b> Data is saved successfully</div>
				<?php
					}
				}
				?>
                <div class="card-body">
 
<form method="post">
	@csrf
	<?php
	$get_last=DB::select("select * from borrower where uid='$uid' order by borrower_id limit 1");
	if(count($get_last)>0){
	foreach($get_last as $get_last2){
	$last_borrower_id=$get_last2->borrower_id;
	}
	}else{
	$last_borrower_id=0;	
	}
	?>
                      <div class="mb-3">
                              <label class="form-label">Borrower Id</label>
                              <input type="number" class="form-control" name="borrower_id" value="{{$last_borrower_id+1}}"  required>
                            </div> 
                                
                      <div class="mb-3" style="display:none">
                              <label class="form-label">User Type</label>
                              <select  class="form-control" name="usertype" onchange="usertypefn(this.value)" >
								  <option>--</option>
								  <option selected>Student</option>
								  <option>Employee</option>
						  </select> 
                            </div> 
	<div   id="studentt">
	   <div class="mb-3" >
                              <label class="form-label">Select Course</label>
                        <select required id="inputcourse" name="inputcourse" class="form-control" onchange="get_students()" >
	<option value="">Select</option>
		<?php 
		$getc=DB::select("select * from courses  order by sr asc");
		if(count($getc)>0){
		foreach($getc as $getc2){
		?>
		<option value="{{$getc2->id}}">{{$getc2->name}}</option>
		<?php 
		}
		}
		?>
		 
	  </select>
                            </div> 	
							<script>
							$(document).ready(function() {
    $('.js-example-basic-single').select2();
});
</script>
			   
   <div class="mb-3" >
                              <label class="form-label">Select Student</label>
                              <select  class="js-example-basic-single form-control" style="width:100%" name="students_id" id="select_student" required >
								  <option value="">--</option>
						  </select>
                            </div> 	
	</div>
<button type="submit" name="save_borrower" class="btn   btn-info">Add</button>
                        </form>
                </div>
            </div>
        </div>
       
            <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Categories') }}


                </div>

                <div class="card-body">
          
                  <div class="table-responsive">

                   <table class="table card-table table-vcenter text-nowrap datatable" id="example">
                   	                      <thead>

											  <tr><th>Id</th><th>Type</th><th>Name</th><th>Class</th> <th>Action</th></tr>
								   </thead><tbody>			  <?php
					   if(isset($_GET['del_borrower'])){
						$id=$_GET['del_borrower'];
						   $del=DB::delete("delete from borrower where id=$id");
						   
					   }
$get=DB::select("select * from borrower  ");
if(count($get)>0){
foreach($get as $get2){
?>
		<tr><td>{{$get2->borrower_id}}</td><td>{{$get2->usertype}}</td><td>
			<?php 
	$courses_id=0;
	$section="--";
	if($get2->usertype=='Student'){
		$stuid=$get2->students_id;
	$getss=DB::select("select * from students where id='$stuid' ");
		if(count($getss)>0){
		foreach($getss as $getss2){
		echo $getss2->first_name." ".$getss2->last_name." S/D/o ". $getss2->father_name;
			$courses_id=$getss2->course; 
		}
		}
	}
	?>
			</td>	
			
			<td>
				<?php 
	
	$geet_class=DB::Select("select * from courses where id='$courses_id'");
	if(count($geet_class)>0){
	foreach($geet_class as $geet_class2){
	echo $geet_class2->name;	
	}
	}
	?>
			  </td>
			<td>
				<a href="?del_borrower={{$get2->id}}" class="btn btn-sm btn-success">Delete</a>
			</td></tr>						  
											  <?php 
}
}
											  
											  ?>
                
                 
                   	</tbody>
                   </table>
               </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
	function usertypefn(key){
	if(key=="Student"){
	document.getElementById("studentt").style.display="";	
	}else{
		alert("we are working on this feature");
	}
	}
	
		function get_divison(val){
		var xhttp = new XMLHttpRequest();
xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
       // Typical action to be performed when the document is ready:
       document.getElementById("devision").innerHTML = xhttp.responseText;
    }
};
xhttp.open("GET", "https://bscollegeltr.in/api/get_devision/{{$uid}}/"+val, true);
xhttp.send();
	}
	function get_students(val){
		var coursess_id=document.getElementById("inputcourse").value;
 
		
		var xhttp = new XMLHttpRequest();
xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
       // Typical action to be performed when the document is ready:
       document.getElementById("select_student").innerHTML = xhttp.responseText;
    }
};
xhttp.open("GET", "https://bscollegeltr.in/api/get_students/{{$uid}}/"+coursess_id, true);
xhttp.send();
	}
</script>
@endsection
