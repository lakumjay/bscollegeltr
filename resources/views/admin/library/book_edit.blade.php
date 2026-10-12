@extends('layouts.appadmin')

@section('title')
Library::Book Master
@endsection
@section('content')
<?php 
	 
   $uid=Auth::user()->id; 
  
// .................
$user_id=Auth::user()->id;
$session_id=Auth::user()->active_session_id;
 
 
?>
<div class="container">
    <div class="row justify-content-center">
    
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Add Book') }}


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
                              <input type="text" class="form-control" name="AccessionNumber" value="{{$data->AccessionNumber}}" placeholder="Enter Accession Number" required>
                            </div> 
                            <div class="col-sm-4 form-group">
                              <label class="form-label">Edition</label>
                              <input type="text" class="form-control" name="ClassificationNumber" value="{{$data->ClassificationNumber}}" placeholder="Enter Addison" required>
                            </div> 

                                 <div class="col-sm-4 form-group">
                              <label class="form-label">Category</label>
                              <select type="text" class="form-control" name="library_categories_id"    required>
@foreach($LibraryCategory as $LibraryCategory2)
<option value="{{$LibraryCategory2->id}}" <?php if($data->library_categories_id==$LibraryCategory2->id){ echo " selected "; } ?>>{{$LibraryCategory2->name}}</option>
@endforeach
                              </select>
                            </div> 
                        <div class="col-sm-4 form-group">
                              <label class="form-label">Book Title</label>
                              <input type="text" class="form-control" name="BookTitle" value="{{$data->BookTitle}}" placeholder="Enter Book Title" required>
                            </div> 

                             <div class="col-sm-4 form-group">
                              <label class="form-label">Book Langauge</label>
                              <input type="text" class="form-control" name="Langauge" value="{{$data->Langauge}}" placeholder="Enter Book Langauge" required>
                            </div>  
                            <div class="col-sm-4 form-group">
                              <label class="form-label">Author</label>
                              <select type="text" class="form-control" name="library_authors_id"  required>
@foreach($LibraryAuthor as $LibraryAuthor2)
<option value="{{$LibraryAuthor2->id}}" <?php if($data->library_authors_id==$LibraryAuthor2->id){ echo " selected "; } ?> >{{$LibraryAuthor2->name}}</option>
@endforeach
                              </select>
                            </div>  
                            <div class="col-sm-4 form-group">
                              <label class="form-label">Book Publisher</label>
                              <select type="text" class="form-control" name="library_publishers_id"   required>
@foreach($LibraryPublisher as $LibraryPublisher2)
<option value="{{$LibraryPublisher2->id}}" <?php if($data->library_publishers_id==$LibraryPublisher2->id){ echo " selected "; } ?> >{{$LibraryPublisher2->name}}</option>
@endforeach
                              </select>
                            </div> 
                            <div class="col-sm-4 form-group">
                              <label class="form-label">ISBN No</label>
                              <input type="text" class="form-control" name="ISBN_No" value="{{$data->ISBN_No}}" placeholder="Enter ISBN No." required>
                            </div> 
                            <div class="col-sm-4 form-group">
                              <label class="form-label">Volume No</label>
                              <input type="text" class="form-control" name="VolumeNo" value="{{$data->VolumeNo}}" placeholder="Enter Volume No." required>
                            </div> 
                            <div class="col-sm-4 form-group">
                              <label class="form-label">Pages</label>
                              <input type="text" class="form-control" name="Page" value="{{$data->Page}}" placeholder="Enter Pages" required>
                            </div> 
                            <div class="col-sm-4 form-group">
                              <label class="form-label">Rack</label>
                              <select type="text" class="form-control" name="library_racks_id"   placeholder="Enter Pages" required>
@foreach($LibraryRack as $LibraryRack2)
<option value="{{$LibraryRack2->id}}" <?php if($data->library_racks_id==$LibraryRack2->id){ echo " selected "; } ?> >{{$LibraryRack2->name}}</option>
@endforeach
                              </select>
                            </div>
                            <div class="col-sm-4 form-group">
                              <label class="form-label">Row Number</label>
                              <input type="text" class="form-control" name="RowNumber" value="{{$data->RowNumber}}" placeholder="Enter Row Number" required>
                            </div> 
                            <div class="col-sm-4 form-group">
                              <label class="form-label">Price</label>
                              <input type="text" class="form-control" name="Price" value="{{$data->Price}}" placeholder="Enter Price" required>
                            </div> 
                            <div class="col-sm-4 form-group">
                              <label class="form-label">Status</label>
                              <select type="text" class="form-control" name="Status"   required>
                                <option value="active" <?php if($data->Status=="active"){ echo " selected "; } ?>>Active</option>
                                <option value="lost" <?php if($data->Status=="lost"){ echo " selected "; } ?>>Lost</option>
                                <option value="damage" <?php if($data->Status=="damage"){ echo " selected "; } ?>>Damage</option>
                              </select>
                            </div>         
                            </div>
                            <br>
<button class="btn   btn-info">Save</button>
                        </form>
                </div>
            </div>
        </div>
             
    </div>
</div>
@endsection
