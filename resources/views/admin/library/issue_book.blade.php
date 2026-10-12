 @extends('layouts.appadmin')
<?php 
 
   $uid=Auth::user()->id; 
  
  
 
$session_id=Auth::user()->active_session_id;

 
 
?>
<script>
$(document).ready(function() {
    $("div.desc").hide();
    $("input[name$='cars']").click(function() {
        var test = $(this).val();
        $("div.desc").hide();
        $("#" + test).show();
    });
});
	
	
	function usertype(val){
		
window.location='?'+val;
		
		
	}
	function get_divison(val){
		var xhttp = new XMLHttpRequest();
xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
       // Typical action to be performed when the document is ready:
       document.getElementById("devision").innerHTML = xhttp.responseText;
    }
};
xhttp.open("GET", "https://web.proschoolerp.com/public/api/get_devision/{{$uid}}/"+val, true);
xhttp.send();
	}
	function getBooks(val,id){
		var xhttp = new XMLHttpRequest();
xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
       // Typical action to be performed when the document is ready:
       document.getElementById("book_id"+id).innerHTML = xhttp.responseText;
    }
};
xhttp.open("GET", "https://bscollegeltr.in/api/get_books/{{$uid}}/"+val, true);
xhttp.send();	
	}
</script>
@section('title')

Library::Author Master
@endsection
@section('content')

     <div class="row justify-content-center">
     
            <div class="col-md-11">
            <div class="card">
                <div class="card-header">{{ __('Issue_Books') }}
<div style="float: right;text-align: right;width: 100%">
	<a href="#" id="myBtn" class="btn btn-sm  btn-info">Collect Book</a>
 </div>
                </div>
<!----------Return------------>
				<div id="myModal" class="modal">
<script>
	function get_data_of_issued(){
	 
	var book_id = document.getElementById("book_id_2").value;
		 
////////
		var xhttp = new XMLHttpRequest();
xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
       // Typical action to be performed when the document is ready:
       document.getElementById("data_of_issued").innerHTML = xhttp.responseText;
    }
};
 xhttp.open("GET", "https://bscollegeltr.in/api/get_data_of_issued/{{$uid}}/"+book_id, true);
xhttp.send();
		//////////
	}
		function get_data_of_issued_2(book_id){
	 
	//var book_id = document.getElementById("book_id_2").value;
		 
////////
		var xhttp = new XMLHttpRequest();
xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
       // Typical action to be performed when the document is ready:
       document.getElementById("data_of_issued").innerHTML = xhttp.responseText;
    }
};
 xhttp.open("GET", "https://bscollegeltr.in/api/get_data_of_issued/{{$uid}}/"+book_id, true);
xhttp.send();
		//////////
	}
	function collect_book_with_issue_id(issue_id){
 
		var return_date = document.getElementById("return_date").value;
window.location="?collect_book_with_id="+issue_id+"&return_date="+return_date+"&return";
	}
					</script>
  <!-- Modal content -->
  <div class="modal-content">
    <span style="float:right" class="close">&times;</span>
 <div class="row">
	    <div class=" col-sm-6">
                              <label class="form-label">Accession Number</label>
                              <input type="number" class="form-control" name="borrower_id" onchange="get_book_det_2(this.value); " required>
                            </div> 
   <div class="col-sm-6">
                              <label class="form-label">Book Title</label>
                              <input  class="form-control" value="" id="book_title_2" readonly  required>
                            </div> 
				  <input type="hidden" class="form-control" value="" id="book_id_2" readonly name="book_id" required>
                  

	 
	   <div class="col-sm-6">
                              <label class="form-label">Return Date</label>
                              <input  class="form-control" value="{{date('Y-m-d')}}" type="date" name="return_date"  id="return_date"  required>
                            </div> 
	   <div class="col-sm-6">
                              <label class="form-label"><br></label>
		   <a class="btn btn-sm btn-info" onclick="get_data_of_issued()" >Get Data</a>
                            </div> 
	 
	</div>
	  <div id="data_of_issued"></div>
					
					</div>

</div>
				
				<!----------Return END------------>

                <div class="card-body">
  <!-- Select_Type: Student  <input type=radio name="usertype" value="student" <?php if(isset($_GET['student'])){ echo " checked "; }   ?> onchange="usertype(this.value)" > &nbsp; employee 
					<input type=radio name="usertype" value="emp"    <?php if(isset($_GET['emp'])){ echo " checked "; }   ?>     onchange="usertype(this.value)"  >
					
					--> 
					<?php 
		if(isset($_GET['collect_book_with_id'])){
		$id=$_GET['collect_book_with_id'];
		$return_date=$_GET['return_date'];
							$update=DB::update("update library_issue_books set status='returned' , return_date='$return_date' where id='$id'  ");
				if($update>0){
							?>
					<div class="alert alert-success"> <b>Success!</b> Book is collected Successfuly </div>
				<?php 
							}
		}
					
					////////
					if(isset($_POST['issue_sae'])){
$students_id=$_POST['students_id'];
$book_id=$_POST['book_id'];
$issue_date=$_POST['issue_date'];
$issue_by=$_POST['issue_by'];
$due_date	=$_POST['due_date'];
						$check=DB::select("select * from library_issue_books where book_id='$book_id'  ");
						if(count($check)>0){
					?>
					<div class="alert alert-danger"><b>Warning!</b> Book is already issued</div>
					<?php 		
						}else{
						$data=array(
'students_id'=>$students_id,
'book_id'=>$book_id,
'issue_date'=>$issue_date,
'issue_by'=>$issue_by,
'due_date'=>$due_date,'uid'=>$uid
						);
						$ins=DB::table("library_issue_books")->insert($data);
						if($ins>0){
						?>
					<div class="alert alert-success"><b>Success!</b> Book is issued successfuly</div>
					<?php 
						}
						}
					}
										if(isset($_GET['issue'])){
?><form method="post">
	@csrf
		<div class="row">			
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">{{ __('Borrower Details') }}


                </div>
 
                <div class="card-body">


 <div class="row">
                      <div class=" col-sm-6">
                              <label class="form-label">Enter Borrower Id</label>
                              <input type="number" class="form-control" name="borrower_id" onchange="get_borrower_det(this.value)" required>
                            </div> 
                                
               <div class="col-sm-6">
                              <label class="form-label">Student Name</label>
                              <input  class="form-control" value="" id="stu_name" readonly  required>
                            </div> 
	 				  <input type="hidden" class="form-control" value="" id="students_id" readonly name="students_id" required>

	</div>
	 
                      
                </div>
            </div>
        </div>
				<div class="col-md-6">
            <div class="card">
                <div class="card-header">{{ __('Book Details') }}


                </div>
 
                <div class="card-body row">
    <div class=" col-sm-6">
                              <label class="form-label">Accession Number</label>
                              <input type="number" class="form-control" name="borrower_id" onchange="get_book_det(this.value)" required>
                            </div> 
   <div class="col-sm-6">
                              <label class="form-label">Book Title</label>
                              <input  class="form-control" value="" id="book_title" readonly  required>
                            </div> 
				  <input type="hidden" class="form-control" value="" id="book_id" readonly name="book_id" required>

                </div>
            </div>
        </div>
			
					</div>   
					<br>
					<div class="col-md-8" style="margin:auto">
            <div class="card">
                <div class="card-header">{{ __('Other Details') }}


                </div>
 
                <div class="card-body row">
    <div class=" col-sm-4">
                              <label class="form-label">Issue Date</label>
                              <input type="date" class="form-control" value="{{date('Y-m-d')}}" name="issue_date"  required>
                            </div> 
   <div class="col-sm-4">
                              <label class="form-label">Issue By</label>
                              <input  class="form-control" name="issue_by"    >
                            </div> 
					  <div class="col-sm-4">
                              <label class="form-label">Due Date</label>
                              <input  type="date" class="form-control"  name="due_date"   >
                            </div> 
                </div>
            </div>
        </div><br>
					<center>
						<input type="submit" name="issue_sae" class="btn btn-info" value="Issue">
					</center>
					</form>
					<?php
											
										}
					
					////////
					if(isset($_GET['issuessss'])){
					
					?>
					<form method="post">
						@csrf 
<div class="row g-3 align-items-center">
  <div class="col-auto">
    <label for="inputcourse" class="col-form-label">Select Course</label>
  </div>
  <div class="col-auto">
    <select required id="inputcourse" name="inputcourse" class="form-control" onchange="get_divison(this.value)" >
	<option value="">Select</option>
		<?php 
		$getc=DB::select("select * from courses  order by name asc");
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
  <div class="col-auto">
    <label for="inputcourse" class="col-form-label">  Division</label>
  </div>
  <div class="col-auto">
    <select required id="devision" name="division" class="form-control"  >
		<option value="">Select</option> 
	  </select>
  </div>
	  <div class="col-auto">
    <label for="inputcourse" class="col-form-label">   </label>
  </div>
  <div class="col-auto">
    <input type="submit"    name="get_list" value="Get List" class="btn btn-info"  >
	  
  </div>
</div>					
     
					</form> 
                </div>
				
	<?php 
				if(isset($_POST['issue_book'])){
$students_id=$_POST['students_id'];
$book_id=$_POST['book_id'];
$issue_date=$_POST['issue_date'];
$remarks=$_POST['remarks'];
					$data=array(
'students_id'=>$students_id,
'book_id'=>$book_id,
'issue_date'=>$issue_date,
'remarks'=>$remarks,
						'uid'=>$uid
					);
					$ins=DB::Table("library_issue_books")->insert($data);
					if($ins>0){
					?>
				<script>
					alert("Book is successfully issued");
					window.location="";
				</script>
				<?php 
					}
				}
if(isset($_POST['get_list'])){	
  $inputcourse=$_POST['inputcourse'];
$division=$_POST['division'];

	 $getList=DB::select("select * from students where  batch_id='$inputcourse'  " ); 
				
if(count($getList)>0){
	
?>
				
<table class="table">
	<thead>
  <tr><th>Roll No</th><th>Name</th><th>Father Name</th><th>Action</th></tr>
	</thead>
	<tbody>
<?php 
		
	foreach($getList as $getList2){
		
	?>
		<tr> <td>{{$getList2->RollNo}}</td> <td>{{$getList2->StudentName}}</td> <td>{{$getList2->FatherName}}</td>  
			
			<td> 
			<!-- Button trigger modal -->
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal{{$getList2->id}}">
Issue Book
</button>

<!-- Modal -->
<div class="modal fade" id="exampleModal{{$getList2->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Issue Book - {{$getList2->StudentName}}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
			  <form method="post">@csrf
      <div class="modal-body">
       <input type="hidden" name="students_id" value="{{$getList2->id}}">
	
  <div class="form-group">
    <label for="cat">Select Category</label>
    <select  class="form-control" id="cat" onchange="getBooks(this.value,{{$getList2->id}})" >
		<option value="">Select</option>
		<?php 
		$getcat=DB::select("select * from library_categories  ");
		if(count($getcat)>0){
		foreach($getcat as $getcat2){
		?>
		<option value="{{$getcat2->id}}">{{$getcat2->name}}</option>
		
		<?php 
		}
		}
		
		?>
	  </select>
   </div>
   <div class="form-group">
    <label for="book_id">Select Book</label>
    <select  class="form-control" id="book_id{{$getList2->id}}" name="book_id"  >
		<option value="">Select</option>
	 
	  </select>
   </div>
		   <div class="form-group">
    <label for="issue_date">Issue date</label>
    <input type="date" class="form-control" id="issue_date" name="issue_date"  >
 	  
   </div>  
		  	   <div class="form-group">
    <label for="remarks">Remarks</label>
    <input type="text" class="form-control" id="remarks" name="remarks"  >
 	  
   </div>  
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" name="issue_book" class="btn btn-primary">Save changes</button>
      </div>
		  </form>
    </div>
  </div>
</div></td></tr> 
		 <?php 
		
	}
		
		?>		
	</tbody>
	
 </table>	
 	<?php 
	
	
}
	}			
						
					}
					if(isset($_GET['return'])){
						if(isset($_GET['collect'])){
						$id=$_GET['collect'];
							$mytime = Carbon\Carbon::now();
$date= $mytime->toDateString();
							
							$update=DB::update("update library_issue_books set status='returned' , return_date='$date' where id='$id'  ");
							if($update>0){
							?>
				<script>
					
					alert("Book is collected successfully");
					window.location="?return";
					
				</script>
				<?php 
							}
						}
						if(isset($_GET['type'])){
							$type=$_GET['type'];
												$get=DB::Select("select * from library_issue_books where status='$type' ");

						}else{
					$get=DB::Select("select * from library_issue_books ");
						}
						if(count($get)>0){
								?>
				 <button id="btnExport" onClick="fnExcelReport()">Export to xls</button>
		 
				<div id="content">
				<table class="table card-table table-vcenter text-nowrap datatable" id="example">
					<thead>
					<tr><th>Book Category</th><th>Book</th><th>Student Name </th><th>Issue Date</th><th>Status</th><th>Return Date</th></tr>
						</thead><tbody>
					
				<?php 
						foreach($get as $get2){
						?>
					<tr>
						<td>{{get_book_cat($get2->book_id)}}</td><td>{{get_book($get2->book_id)}}</td>
						<td>{{get_student($get2->students_id)}}</td><td>{{$get2->issue_date}}</td>
						<td>{{$get2->status}}</td><td>
						<?php 
							if($get2->status=='issued'){
								
							
							?>
						Not Collected Yet
						<?php 
								
							}else{
							?>
						<b style="color:green">Collected on {{$get2->return_date}}</b>
						<?php 
							}
							
							?>
						</td>
					
					</tr>
				<?php 
						}
								?>
					</tbody>
				</table>
				</div>
				<?php 
						}
					}
				if(isset($_GET['damage_lost'])){
					$get=DB::select("SELECT * FROM `library_books` where  status='lost' or status='damage' ");
					if(count($get)>0){
						?>
				 <button id="btnExport" onClick="fnExcelReport()">Export to xls</button>
		 
				<div id="content">
				<table class="table card-table table-vcenter text-nowrap datatable" id="example">
					<thead>
						<tr><th>Book Title</th><th>Accession No.</th><th>Language</th><th>Status</th></tr>
						</thead><tbody>
					<?php
						foreach($get as $get2){
						?>
					<tr><td>{{$get2->BookTitle}}</td><td>{{$get2->AccessionNumber}}</td><td>{{$get2->Langauge}}</td><td>{{$get2->Status}}</td></tr>
					<?php 
						}
						?>
					</tbody></table></div>
				<?php
					}
				}
					
				if(isset($_GET['Circulation'])){
					$get=DB::select("SELECT * FROM `library_books` where  status='active' ");
					if(count($get)>0){
						?>
				 <button id="btnExport" onClick="fnExcelReport()">Export to xls</button>
		 
				<div id="content">
				<table class="table card-table table-vcenter text-nowrap datatable" id="example">
					<thead>
						<tr><th>Book Title</th><th>Accession No.</th><th>Language</th><th>Status</th></tr>
						</thead><tbody>
					<?php
						foreach($get as $get2){
						?>
					<tr><td>{{$get2->BookTitle}}</td><td>{{$get2->AccessionNumber}}</td><td>{{$get2->Langauge}}</td><td>
						<?php 
							$book_id=$get2->id;
							$check_issued=DB::select("select * from library_issue_books where book_id='$book_id' and status='issued'  ");
							if(count($check_issued)>0){
								echo " Issued ";
							}else{
							echo " Active ";	
							}
							?>
						
						 
						
						
						</td></tr>
					<?php 
						}
						?>
					</tbody></table></div>
				<?php
					}
				}		
					
 	?>
				
				
            </div>
        </div>
 </div>
				<?php
				if(isset($_GET['recieve'])){
				?>
				<script>
					var modal = document.getElementById("myModal");
  modal.style.display = "block";

				</script>
				<?php 
				}
				?>
<script>
// Get the modal
var modal = document.getElementById("myModal");

// Get the button that opens the modal
var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks the button, open the modal 
btn.onclick = function() {
  modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
  modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
}
</script>
<style>
 
/* The Modal (background) */
.modal {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 1; /* Sit on top */
  padding-top: 100px; /* Location of the box */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgb(0,0,0); /* Fallback color */
  background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Modal Content */
.modal-content {
  background-color: #fefefe;
  margin: auto;
  padding: 20px;
  border: 1px solid #888;
  width: 50%;
}

/* The Close Button */
.close {
  color: #aaaaaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
}

.close:hover,
.close:focus {
  color: #000;
  text-decoration: none;
  cursor: pointer;
}
</style>
 <script>
	function get_borrower_det(key){
				var xhttp = new XMLHttpRequest();
xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
       // Typical action to be performed when the document is ready:
       document.getElementById("stu_name").value = xhttp.responseText;
    }
};
xhttp.open("GET", "https://bscollegeltr.in/api/get_borrower_details/{{$uid}}/"+key, true);
xhttp.send();
		////////
			var xhttp2 = new XMLHttpRequest();
xhttp2.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
       // Typical action to be performed when the document is ready:
       document.getElementById("students_id").value = xhttp2.responseText;
    }
};
xhttp2.open("GET", "https://bscollegeltr.in/api/get_borrower_details_id/{{$uid}}/"+key, true);
xhttp2.send();
	}
	 
	 function get_book_det(key){
				var xhttp = new XMLHttpRequest();
xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
       // Typical action to be performed when the document is ready:
       document.getElementById("book_title").value = xhttp.responseText;
    }
};
xhttp.open("GET", "https://bscollegeltr.in/api/get_book_details/{{$uid}}/"+key, true);
xhttp.send();
		 ///////////
		 				var xhttp2 = new XMLHttpRequest();
xhttp2.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
       // Typical action to be performed when the document is ready:
       document.getElementById("book_id").value = xhttp2.responseText;
    }
};
xhttp2.open("GET", "https://bscollegeltr.in/api/get_book_details_id/{{$uid}}/"+key, true);
xhttp2.send();
	}
	 	function get_borrower_det_2(key){
				var xhttp = new XMLHttpRequest();
xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
       // Typical action to be performed when the document is ready:
       document.getElementById("stu_name_2").value = xhttp.responseText;
    }
};
xhttp.open("GET", "https://bscollegeltr.in/api/get_borrower_details/{{$uid}}/"+key, true);
xhttp.send();
		////////
			var xhttp2 = new XMLHttpRequest();
xhttp2.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
       // Typical action to be performed when the document is ready:
       document.getElementById("students_id_2").value = xhttp2.responseText;
    }
};
xhttp2.open("GET", "https://bscollegeltr.in/api/get_borrower_details_id/{{$uid}}/"+key, true);
xhttp2.send();
	}
	 	 function get_book_det_2(key){
				var xhttp = new XMLHttpRequest();
xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
       // Typical action to be performed when the document is ready:
       document.getElementById("book_title_2").value = xhttp.responseText;
    }
};
xhttp.open("GET", "https://bscollegeltr.in/api/get_book_details/{{$uid}}/"+key, true);
xhttp.send();
		 ///////////
		 				var xhttp2 = new XMLHttpRequest();
xhttp2.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
       // Typical action to be performed when the document is ready:
       document.getElementById("book_id_2").value = xhttp2.responseText;
		var book2_id=xhttp2.responseText;
					 get_data_of_issued_2(book2_id);

    }
};
xhttp2.open("GET", "https://bscollegeltr.in/api/get_book_details_id/{{$uid}}/"+key, true);
xhttp2.send();
	}
					</script>
	
@endsection
