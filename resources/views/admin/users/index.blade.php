@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Users Management</h1>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Add New User</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Allocated Courses</th>
                                <th>Progress</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone }}</td>
                                <td>
                                    <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        @forelse($user->allocatedCourses as $course)
                                            <span class="badge bg-primary">{{ $course->name }}</span>
                                        @empty
                                            <span class="text-muted">No courses</span>
                                        @endforelse
                                    </div>
                                    <button class="btn btn-sm btn-outline-primary mt-1" data-bs-toggle="modal" data-bs-target="#allocateModal{{ $user->id }}">
                                        <i class="fas fa-plus"></i> Allocate
                                    </button>
                                </td>
                                <td>
                                    @if($user->allocatedCourses->count() > 0)
                                        <a href="{{ route('admin.users.course-details', $user) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-chart-line"></i> View Progress
                                        </a>
                                    @else
                                        <span class="text-muted">No progress</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-{{ $user->is_active ? 'secondary' : 'success' }}">
                                            {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Course Allocation Modal -->
                            <div class="modal fade" id="allocateModal{{ $user->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Allocate Course to {{ $user->name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('admin.users.allocate-course', $user) }}" method="POST">
                                            @csrf
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Select Course</label>
                                                    <select name="course_id" class="form-select" required>
                                                        <option value="">Choose a course...</option>
                                                        @foreach($courses as $course)
                                                            @if(!$user->allocatedCourses->contains($course->id))
                                                                <option value="{{ $course->id }}">{{ $course->name }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                                
                                                @if($user->allocatedCourses->count() > 0)
                                                <div class="mb-3">
                                                    <label class="form-label">Currently Allocated Courses</label>
                                                    <div class="list-group">
                                                        @foreach($user->allocatedCourses as $allocatedCourse)
                                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                                            {{ $allocatedCourse->name }}
                                                            <a href="{{ route('admin.users.deallocate-course', [$user, $allocatedCourse->id]) }}" 
                                                               class="btn btn-sm btn-outline-danger"
                                                               onclick="return confirm('Remove this course?')">
                                                                <i class="fas fa-times"></i>
                                                            </a>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Allocate Course</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">No users found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



