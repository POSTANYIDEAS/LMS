@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">{{ $user->name }} - Course Progress</h1>
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Back to Users</a>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h5>User Information</h5>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Name:</strong> {{ $user->name }}</p>
                        <p><strong>Email:</strong> {{ $user->email }}</p>
                        <p><strong>Phone:</strong> {{ $user->phone }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Status:</strong> 
                            <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </p>
                        <p><strong>Total Courses:</strong> {{ $user->allocatedCourses->count() }}</p>
                        <p><strong>Joined:</strong> {{ $user->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@foreach($user->allocatedCourses as $course)
@php
    $totalLessons = $course->topics->sum(function($topic) { return $topic->lessons->count(); });
    $completedLessons = $user->completedLessons()->whereIn('lesson_id', 
        $course->topics->flatMap(function($topic) { return $topic->lessons->pluck('id'); })
    )->count();
    
    $totalTests = $course->topics->sum(function($topic) { return $topic->mcqTests->count(); });
    $completedTests = $user->mcqResults()->whereIn('mcq_test_id', 
        $course->topics->flatMap(function($topic) { return $topic->mcqTests->pluck('id'); })
    )->count();
    
    $totalItems = $totalLessons + $totalTests;
    $completedItems = $completedLessons + $completedTests;
    $progress = $totalItems > 0 ? round(($completedItems / $totalItems) * 100, 2) : 0;
@endphp

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ $course->name }}</h5>
                <div>
                    <span class="badge bg-primary">{{ $progress }}% Complete</span>
                    <span class="badge bg-info">{{ $completedItems }}/{{ $totalItems }} Items</span>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="progress mb-2">
                            <div class="progress-bar bg-success" style="width: {{ $progress }}%"></div>
                        </div>
                        <small class="text-muted">Overall Progress: {{ $progress }}%</small>
                    </div>
                    <div class="col-md-6">
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="border rounded p-2">
                                    <h6 class="mb-0">{{ $completedLessons }}/{{ $totalLessons }}</h6>
                                    <small>Lessons</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border rounded p-2">
                                    <h6 class="mb-0">{{ $completedTests }}/{{ $totalTests }}</h6>
                                    <small>Tests</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border rounded p-2">
                                    <h6 class="mb-0">{{ $course->topics->count() }}</h6>
                                    <small>Topics</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Topics Progress -->
                <h6>Topics Progress</h6>
                @foreach($course->topics as $topic)
                @php
                    $topicLessons = $topic->lessons->count();
                    $topicCompletedLessons = $user->completedLessons()->whereIn('lesson_id', $topic->lessons->pluck('id'))->count();
                    
                    $topicTests = $topic->mcqTests->count();
                    $topicCompletedTests = $user->mcqResults()->whereIn('mcq_test_id', $topic->mcqTests->pluck('id'))->count();
                    
                    $topicTotal = $topicLessons + $topicTests;
                    $topicCompleted = $topicCompletedLessons + $topicCompletedTests;
                    $topicProgress = $topicTotal > 0 ? round(($topicCompleted / $topicTotal) * 100, 2) : 0;
                @endphp
                <div class="border rounded p-3 mb-2">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong>{{ $topic->order }}. {{ $topic->title }}</strong>
                        <span class="badge bg-{{ $topicProgress == 100 ? 'success' : ($topicProgress > 0 ? 'warning' : 'secondary') }}">
                            {{ $topicProgress }}%
                        </span>
                    </div>
                    <div class="progress mb-2" style="height: 6px;">
                        <div class="progress-bar bg-info" style="width: {{ $topicProgress }}%"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">
                                <i class="fas fa-book me-1"></i>Lessons: {{ $topicCompletedLessons }}/{{ $topicLessons }}
                            </small>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">
                                <i class="fas fa-question-circle me-1"></i>Tests: {{ $topicCompletedTests }}/{{ $topicTests }}
                                @if($topicCompletedTests > 0)
                                    @php
                                        $avgScore = $user->mcqResults()->whereIn('mcq_test_id', $topic->mcqTests->pluck('id'))->avg('percentage');
                                    @endphp
                                    (Avg: {{ round($avgScore, 1) }}%)
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endforeach

@if($user->allocatedCourses->count() == 0)
<div class="row">
    <div class="col-12">
        <div class="text-center py-5">
            <i class="fas fa-book fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">No courses allocated</h4>
            <p class="text-muted">This user has no courses allocated yet.</p>
        </div>
    </div>
</div>
@endif
@endsection

