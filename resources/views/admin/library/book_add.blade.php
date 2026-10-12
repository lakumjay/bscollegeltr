@extends('layouts.appadmin')

@section('title')
Library::Book Master
@endsection
@section('content')
<?php 
	 
   $uid=Auth::user()->id; 
  $session_id=Auth::user()->active_session_id;

// .................
$user_id=Auth::user()->id;
 
?>
<div class="container">
    <div class="row justify-content-center">
    
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Add Book') }}
<a href="{{url('admin/library/book')}}"><u>View</u> </a> 

                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

<form method="post">
	@csrf
  <div class="row">
                      <div class="col-sm-4 form-group">
                              <label class="form-label">Accession Number</label>
                              <input type="text" class="form-control" name="AccessionNumber" placeholder="Enter Accession Number" required>
                            </div> 
                            <div class="col-sm-4 form-group">
                              <label class="form-label">Edition</label>
                              <input type="text" class="form-control" name="ClassificationNumber" placeholder="Enter Addison" value="0" >
                            </div> 

                                 <div class="col-sm-4 form-group">
                              <label class="form-label">Category</label>
                              <select type="text" class="form-control" name="library_categories_id"   required>
@foreach($LibraryCategory as $LibraryCategory2)
<option <?php if(isset($_POST['library_categories_id']) and $_POST['library_categories_id']==$LibraryCategory2->id) { echo " active "; }  ?> value="{{$LibraryCategory2->id}}">{{$LibraryCategory2->name}}</option>
@endforeach
                              </select>
                            </div> 
                        <div class="col-sm-4 form-group">
                              <label class="form-label">Book Title</label>
                              <input type="text" class="form-control" name="BookTitle" placeholder="Enter Book Title" required>
                            </div> 

                             <div class="col-sm-4 form-group">
                              <label class="form-label">Book Langauge</label>
                              <input type="text" class="form-control" name="Langauge" placeholder="Enter Book Langauge" 
                              <?php if(isset($_POST['Langauge'])){ ?> value="{{$_POST['Langauge']}}" <?php } ?> required value="Hindi">
                            </div>  
                            <div class="col-sm-4 form-group">
                              <label class="form-label">Author</label>
                              <select type="text" class="form-control" name="library_authors_id"  required>
                            
@foreach($LibraryAuthor as $LibraryAuthor2)
<option   <?php  if(isset($_POST['library_authors_id']) and $_POST['library_authors_id']==$LibraryAuthor2->id ){ ?> active  <?php } ?> 
 value="{{$LibraryAuthor2->id}}">{{$LibraryAuthor2->name}}</option>
@endforeach
                              </select>
                            </div>  
                            <div class="col-sm-4 form-group">
                              <label class="form-label">Book Publisher</label>
                              <select type="text" class="form-control" name="library_publishers_id"   required>
@foreach($LibraryPublisher as $LibraryPublisher2)
<option <?php if(isset($_POST['library_publishers_id']) and $_POST['library_authors_id']==$LibraryAuthor2->id ){ ?> active  <?php } ?> 

 value="{{$LibraryPublisher2->id}}">{{$LibraryPublisher2->name}}</option>
@endforeach
                              </select>
                            </div> 
                            <div class="col-sm-4 form-group">
                              <label class="form-label">ISBN No</label>
                              <input type="text" class="form-control" name="ISBN_No" placeholder="Enter ISBN No." >
                            </div> 
                            <div class="col-sm-4 form-group">
                              <label class="form-label">Volume No</label>
                              <input type="text" class="form-control" name="VolumeNo" placeholder="Enter Volume No." >
                            </div> 
                            <div class="col-sm-4 form-group">
                              <label class="form-label">Pages</label>
                              <input type="text" class="form-control" name="Page" placeholder="Enter Pages" >
                            </div> 
                            <div class="col-sm-4 form-group">
                              <label class="form-label">Rack</label>
                              <select type="text" class="form-control" name="library_racks_id" placeholder="Enter Pages" required onchange="getRackRows(this.value)">
                                <option value="">---</option>
@foreach($LibraryRack as $LibraryRack2)
<option <?php if(isset($_POST['library_racks_id']) and $_POST['library_racks_id']==$LibraryRack2->id ){ ?> active  <?php } ?>   value="{{$LibraryRack2->id}}">{{$LibraryRack2->name}}</option>
@endforeach
                              </select>
                            </div>
                            <div class="col-sm-4 form-group">
                              <label class="form-label">Row Number</label>
          <select type="text" class="form-control" name="RowNumber" id="RowNumber" placeholder="Enter Row Number" required>
          
<option value="">---</option>
                              </select>
                              <script type="text/javascript">
                                function getRackRows(a) {
                                 if(a==""){
       document.getElementById("RowNumber").innerHTML = `<option value="">---</option>
`;

                                }else{
           var xhttp = new XMLHttpRequest();
xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
       // Typical action to be performed when the document is ready:
       document.getElementById("RowNumber").innerHTML = xhttp.responseText;
       getRollNo();
    }
};
xhttp.open("GET", "{{url('api/getLibraryRackRows/'.$uid)}}/"+a, true);
xhttp.send();
}
                               
                                }
                              </script>
                            </div> 
                            <div class="col-sm-4 form-group">
                              <label class="form-label">Price</label>
                              <input type="text" class="form-control" name="Price" placeholder="Enter Price" required>
                            </div> 
                            <div class="col-sm-4 form-group">
                              <label class="form-label">Status</label>
                              <select type="text" class="form-control" name="Status"  required>
                                <option value="active">Active</option>
                                <option value="lost">Lost</option>
                                <option value="damage">Damage</option>
                              </select>
                            </div>         
                            </div>
                            <br>
<button class="btn   btn-info">Add</button>
                        </form>
                </div>
            </div>
        </div>
             
    </div>
</div>
@endsection
