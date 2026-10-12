<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/changetheme/{theme}', function ($theme) {
 setcookie("theme", $theme, time() + (86400 * 3000), "/");  


});

 
Route::get('/get_students/{uid}/{courses_id}', function ($uid,$courses_id) {
 $get=DB::Select("select * from students where  course='$courses_id'");
	if(count($get)>0){
		?>
<option value="">Select</option> 
<?php 
	foreach($get as $get2){
		?>
<option value="<?php echo $get2->id; ?>"><?php echo $get2->first_name." ".$get2->last_name." S/D/o ".$get2->father_name." Roll No: ".$get2->roll_no." Stu. Id.:". $get2->student_id; ?></option>
<?php
	}
	}else{
		?>
<option value="">Not Found</option>
<?php	
	}
 
});
Route::get('/get_books/{uid}/{courses_id}', function ($uid,$courses_id) {
 $get=DB::Select("select * from library_books where  library_categories_id ='$courses_id'");
	if(count($get)>0){
		?>
<option value="">Select</option> 
<?php 
	foreach($get as $get2){
		?>
<option value="<?php echo $get2->id; ?>"><?php echo $get2->BookTitle; ?> Acc. No. <?php echo $get2->AccessionNumber; ?></option>
<?php
	}
	}else{
		?>
<option value="">Not Found</option>
<?php	
	}
 
});

Route::get('/get_data_of_issued/{uid}/{book_id}', function ($uid,$book_id) {
 $get=DB::select("select * from library_issue_books where   book_id='$book_id' and  status='issued' ");
	if(count($get)>0){
		 
		?>
<!--<table class="table">
	<tr><th>Borrower Id</th><th>Name</th><th>Issue Date</th><th>Due Date</th><th>Issued By</th><th>Action</th></tr>-->
<?php 
	foreach($get as $get2){
	?>
	  <div class="row">   <div class=" col-sm-6">
                              <label class="form-label">Enter Borrower Id</label>
                              <input type="number" class="form-control" value="<?php echo Get_borrower_id($get2->students_id); ?>" readonly name="borrower_id" required >
                            </div> 
                                
               <div class="col-sm-6">
                              <label class="form-label">Student Name</label>
                              <input  class="form-control" value="<?php echo get_student2($get2->students_id); ?>"  readonly  required>
                            </div> 
</div><br>
<center>	<a class="btn  btn-info" onclick="collect_book_with_issue_id(<?php echo $get2->id; ?>)">Collect</a> </center>
<!--<tr>
	<td><?php echo Get_borrower_id($get2->students_id); ?></td>
		<td><?php echo get_student2($get2->students_id); ?></td>

	
	<td><?php echo $get2->issue_date; ?></td><td><?php echo $get2->due_date; ?></td>
	<td><?php echo $get2->issue_by; ?></td>
<td>
	<a class="btn btn-sm btn-info" onclick="collect_book_with_issue_id(<?php echo $get2->id; ?>)">Collect</a>
	
	</td>
	</tr>-->
<?php 
	}
		?>
<!--</table>-->
	<?php 
	}else{
	?>
<b style="text-align:center"> No Details Found</b>
<?php 
	}
 
});
Route::get('/get_borrower_details/{uid}/{id}', function ($uid,$id) {
 $get=DB::Select("select * from borrower where  borrower_id='$id' ");
	if(count($get)>0){
	foreach($get as $get2){
	return get_student($get2->students_id);	
	}
	}else{
	echo "Not Found	";
	}
 
 
});
Route::get('/get_borrower_details_id/{uid}/{id}', function ($uid,$id) {
 $get=DB::Select("select * from borrower where  borrower_id='$id' ");
	if(count($get)>0){
	foreach($get as $get2){
	return $get2->students_id;	
	}
	}else{
	echo "Not Found	";
	}
 
 
});
Route::get('/get_book_details/{uid}/{id}', function ($uid,$id) {
 $get=DB::Select("select * from library_books where  AccessionNumber='$id' ");
	if(count($get)>0){
	foreach($get as $get2){
	return $get2->BookTitle;	
	}
	}else{
	echo "Not Found	";
	}
 
 
});
Route::get('/get_book_details_id/{uid}/{id}', function ($uid,$id) {
 $get=DB::Select("select * from library_books where  AccessionNumber='$id' ");
	if(count($get)>0){
	foreach($get as $get2){
	return $get2->id;	
	}
	}else{
	echo "Not Found	";
	}
 
 
});

Route::get('/getLibraryRackRows/{uid}/{id}', function ($uid,$id) {
$get=App\Models\LibraryRack::where('id',$id)->get();
foreach($get as $get2){
	$NumberOfRows=$get2->NumberOfRows;
	for ($i=1; $i <=$NumberOfRows ; $i++) { 
		?>
<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
	<?php
	}
	
}
});