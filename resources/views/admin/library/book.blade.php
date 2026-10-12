@extends('layouts.appadmin')

@section('title')
Library::Author Master
@endsection
@section('content')
<?php 
	 
   $uid=Auth::user()->id; 
  
// .................
$user_id=Auth::user()->id;
 $session_id=Auth::user()->active_session_id;
 
 
?>
     <div class="row justify-content-center">
     
            <div class="col-md-11">
            <div class="card">
                <div class="card-header">{{ __('Books') }}
<div style="float: right;text-align: right;width: 100%">
<a href="book_add" class="btn btn-info"  >Add</a>
</div>
                </div>

                <div class="card-body">
                	@php
$sr=1;
@endphp
					<?php 
				 
					if(isset($_GET['del'])){
						$id=$_GET['del'];
					$del=DB::delete("delete from library_books where id='$id'   ");
					if($del>0){
					?>
				 <script>
					 alert("Book is deleted successfuly");
					 window.location="";
					</script>
					<?php 
					}
					}
					 
					?>
                  <div class="table-responsive">

                   <table class="table card-table table-vcenter text-nowrap datatable" id="example">
                   	                      <thead>

                   	<tr><th>Sr#</th><th>Accession Number</th> <th>Addison</th><th>Category</th><th>Book Title
</th>
<th>Langauge</th><th>Author</th><th>Publisher</th><th>ISBN No
</th><th>Volume No
</th><th>Pages</th><th>Rack</th><th>Row Number
</th><th>Price</th><th>Status</th><th>Action</th>
</tr>
                   </thead><tbody>
                   @foreach($LibraryBook as $data)
<tr>
  <td>{{$sr++}}</td>
  <td>{{$data->AccessionNumber}}</td>
  <td>{{$data->ClassificationNumber}}</td>
  <td>{{$data->LibraryCategory->name}}</td>
  <td>{{$data->BookTitle}}</td>
  <td>{{$data->Langauge}}</td>
  <td>{{$data->LibraryAuthor->name}}</td>
  <td>{{$data->LibraryPublisher->name}}</td>
  <td>{{$data->ISBN_No}}</td>
  <td>{{$data->VolumeNo}}</td>
  <td>{{$data->Page}}</td>
  <td>{{$data->LibraryRack->name}}</td>
  <td>{{$data->RowNumber}}</td>
  <td>{{$data->Price}}</td>
  <td>{{$data->Status}}</td>
	
	
<td><a href="{{url('admin/library/book/edit/'.$data->id)}}" class="btn btn-info btn-sm">Edit</a>
	<a href="?del={{$data->id}}" class="btn btn-danger btn-sm">Delete</a>
	</td>
</tr>
                   @endforeach
                   	</tbody>
                   </table>
               </div>
                </div>
            </div>
        </div>
 </div>
@endsection
