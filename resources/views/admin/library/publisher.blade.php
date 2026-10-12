@extends('layouts.appadmin')

@section('title')
Library::Publisher Master
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
                <div class="card-header">{{ __('Editing Publisher') }}


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
                                
<button class="btn   btn-info">Update</button>
                        </form>
                </div>
            </div>
        </div>
    	@else
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">{{ __('Add Publisher') }}


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
                                
<button class="btn   btn-info">Add</button>
                        </form>
                </div>
            </div>
        </div>
        @endif
            <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Publishers') }}


                </div>

                <div class="card-body">
                	@php
$sr=1;
@endphp
                  <div class="table-responsive">

                   <table class="table card-table table-vcenter text-nowrap datatable">
                   	                      <thead>

                   	<tr><th>Sr#</th><th>Name</th> <th>Action</th></tr>
                   </thead><tbody>
                   	@if(count($LibraryPublisher)>0)
@foreach($LibraryPublisher as $data)
<tr><td>{{$sr++}}</td><td>{{$data->name}}</td>   <td  >
                            
                            <a href="{{url('admin/library/publisher/edit/'.$data->id)}}" class="btn btn-sm btn-info">Edit</a>

                          </td></tr>
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
