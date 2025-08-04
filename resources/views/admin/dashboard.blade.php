@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Welcome to Your Dashboard</h5>
                <p class="card-text">Hello {{ $admin->name }}, you are successfully logged in!</p>
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Your Profile</h6>
                                <p><strong>Name:</strong> {{ $admin->name }}</p>
                                <p><strong>Email:</strong> {{ $admin->email }}</p>
                                <p><strong>Phone:</strong> {{ $admin->phone ?? 'N/A' }}</p>
                                <p><strong>Status:</strong> 
                                    <span class="badge bg-success">Active</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Quick Actions</h6>
                                <p>More features coming soon...</p>
                                <button class="btn btn-primary btn-sm" disabled>View Courses</button>
                                <button class="btn btn-secondary btn-sm" disabled>My Progress</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
