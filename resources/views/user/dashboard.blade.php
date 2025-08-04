<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - LMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .dashboard-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
        }
        .course-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            border-radius: 15px;
            overflow: hidden;
        }
        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .progress-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
        }
        .stats-card {
            background: linear-gradient(45deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
        }
        .sidebar {
            background: #2c3e50;
            min-height: 100vh;
            padding-top: 2rem;
        }
        .sidebar .nav-link {
            color: #ecf0f1;
            padding: 0.75rem 1.5rem;
            margin: 0.25rem 0;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: #3498db;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="dashboard-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-0"><i class="fas fa-graduation-cap me-3"></i>Welcome back, {{ $user->name }}!</h1>
                    <p class="mb-0 mt-2">Continue your learning journey</p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="progress-circle" style="background: rgba(255,255,255,0.2);">
                        {{ $overallProgress }}%
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 sidebar">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('user.dashboard') }}">
                            <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#courses">
                            <i class="fas fa-book me-2"></i> My Courses
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#progress">
                            <i class="fas fa-chart-line me-2"></i> Progress
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('user.logout') }}">
                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Main Content -->
            <main class="col-md-10 px-4 py-4">
                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="stats-card">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h3 class="mb-0">{{ $allocatedCourses->count() }}</h3>
                                    <p class="mb-0">Enrolled Courses</p>
                                </div>
                                <i class="fas fa-book fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card" style="background: linear-gradient(45deg, #a8edea 0%, #fed6e3 100%);">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h3 class="mb-0">{{ $overallProgress }}%</h3>
                                    <p class="mb-0">Overall Progress</p>
                                </div>
                                <i class="fas fa-chart-pie fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card" style="background: linear-gradient(45deg, #ffecd2 0%, #fcb69f 100%);">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h3 class="mb-0">{{ $allocatedCourses->where('pivot.completed', true)->count() }}</h3>
                                    <p class="mb-0">Completed</p>
                                </div>
                                <i class="fas fa-trophy fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card" style="background: linear-gradient(45deg, #a18cd1 0%, #fbc2eb 100%);">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h3 class="mb-0">{{ $allocatedCourses->count() - $allocatedCourses->where('pivot.completed', true)->count() }}</h3>
                                    <p class="mb-0">In Progress</p>
                                </div>
                                <i class="fas fa-clock fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Courses Section -->
                <div class="row" id="courses">
                    <div class="col-12">
                        <h2 class="mb-4"><i class="fas fa-book-open me-2"></i>My Courses</h2>
                    </div>
                    @forelse($allocatedCourses as $course)
                    @php
                        $courseProgress = 0;
                        foreach($course->topics as $topic) {
                            $totalItems = $topic->lessons->count() + $topic->mcqTests->count();
                            $completedItems = $user->completedLessons()->whereIn('lesson_id', $topic->lessons->pluck('id'))->count() + 
                                            $user->mcqResults()->whereIn('mcq_test_id', $topic->mcqTests->pluck('id'))->count();
                            if($totalItems > 0) {
                                $courseProgress += ($completedItems / $totalItems) * 100;
                            }
                        }
                        $courseProgress = $course->topics->count() > 0 ? round($courseProgress / $course->topics->count(), 2) : 0;
                    @endphp
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card course-card h-100">
                            @if($course->image)
                            <img src="{{ asset($course->image) }}" class="card-img-top" style="height: 200px; object-fit: cover;" alt="{{ $course->name }}">
                            @endif
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">{{ $course->name }}</h5>
                                <p class="card-text flex-grow-1">{{ Str::limit($course->description, 100) }}</p>
                                
                                <!-- Progress Bar -->
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="small">Progress</span>
                                        <span class="small">{{ $courseProgress }}%</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-success" style="width: {{ $courseProgress }}%"></div>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="fas fa-list me-1"></i>{{ $course->topics->count() }} Topics
                                    </small>
                                    <a href="{{ route('user.course.view', $course->id) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-play me-1"></i>Continue
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="fas fa-book fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">No courses allocated yet</h4>
                            <p class="text-muted">Contact your administrator to get courses assigned.</p>
                        </div>
                    </div>
                    @endforelse
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
