@extends('layouts.appadmin')

@section('title')
Library::Rack Master
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
    	@if(isset($edit))
 <div class="col-md-4">
            <div class="card">
                <div class="card-header">{{ __('Editing Rack') }}


                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
<form method="post">
	@csrf
                      <div class="mb-3">
                              <label class="form-label">Name</label>
                              <input type="text" class="form-control" name="name" value="{{$data->name}}" placeholder="Enter Category name" required>
                            </div> 
                                <div class="mb-3">
                              <label class="form-label">No. Of Rows</label>
                              <input type="number" class="form-control" name="NumberOfRows" value="{{$data->NumberOfRows}}" placeholder="Enter No. of Rows" required min="1">
                            </div>   
<button class="btn   btn-info">Update</button>
                        </form>
                </div>
            </div>
        </div>
    	@else
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">{{ __('Add Rack') }}


                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
<form method="post">
	@csrf
                      <div class="mb-3">
                              <label class="form-label">Name</label>
                              <input type="text" class="form-control" name="name" placeholder="Enter Category name" required>
                            </div> 
                            <div class="mb-3">
                              <label class="form-label">No. Of Rows</label>
                              <input type="number" class="form-control" name="NumberOfRows" placeholder="Enter No. of Rows" required>
                            </div> 
                                
<button class="btn   btn-info">Add</button>
                        </form>
                </div>
            </div>
        </div>
        @endif
            <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Racks') }}


                </div>

                <div class="card-body">
                	@php
$sr=1;
@endphp
                  <div class="table-responsive">

                   <table class="table card-table table-vcenter text-nowrap datatable">
                   	                      <thead>

                   	<tr><th>Sr#</th><th>Name</th><th>No. Of Rack</th><th>Public Link <th>Action</th></tr>
                   </thead><tbody>
                   	@if(count($LibraryRack)>0)
@foreach($LibraryRack as $data)
<tr><td>{{$sr++}}</td><td>{{$data->name}}</td><td>{{$data->NumberOfRows}}</td>   
<td  >  <a href="{{url('admin/library/rack/edit/'.$data->id)}}" class="btn btn-sm btn-info">Edit</a>  </td>
<td>
<a href="{{url('library/books')}}?u={{$uid}}&rack={{$data->id}}">Link</a> 


</td> 
</tr>
@endforeach

                   	@endif
                   	</tbody>
                   </table>
               </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
