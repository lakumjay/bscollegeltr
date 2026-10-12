@extends('layouts.appadmin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Staff Management
                    <a href="{{ route('admin.staff.create') }}" class="btn btn-info float-right mx-2">Add Staff</a>
                    <a href="{{ route('admin.staff.attendance.mark') }}" class="btn btn-primary float-right">Mark Attendance</a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <table class="table" id="example2">
                        <thead>
                            <tr>
                                <th>Staff ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Recurring income</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($staff as $member)
                            <tr>
                                <td>{{ $member->staff_id }}</td>
                                <td>{{ $member->first_name }} {{ $member->last_name }}</td>
                                <td>{{ $member->email }}</td>
                                <td>{{ $member->role }}</td>
                                <td>@if($member->salaryStructure){{ $member->salaryStructure->currency }} {{ number_format((float) $member->salaryStructure->basic_pay + (float) $member->salaryStructure->da + (float) $member->salaryStructure->hra + (float) $member->salaryStructure->additional_earnings, 2) }}@else Not configured @endif</td>
                                <td>{{ $member->status }}</td>
                                <td>
                                    <a href="{{ route('admin.staff.show', $member) }}" class="btn btn-primary btn-sm">View</a>
                                    <a href="{{ route('admin.staff.edit', $member) }}" class="btn btn-warning btn-sm">Edit</a>
                                    <form method="post" action="{{ route('admin.staff.destroy', $member) }}" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                    <a href="{{ route('admin.staff.transfer_certificate', $member) }}" class="btn btn-success btn-sm">TC</a>
                                    <a href="{{ route('admin.staff.id_card', $member) }}" class="btn btn-info btn-sm">ID Card</a>
                                    <a href="{{ route('admin.staff.attendance.view', $member) }}" class="btn btn-secondary btn-sm">Attendance</a>
                                </td>
                            </tr>
                            @endforeach
                            @if($staff->isEmpty())
                                <tr>
                                    <td colspan="7" class="text-center">No staff found</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
