<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $course->name }} - LMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .course-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
        }
        .topic-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
        }
        .lesson-item, .test-item {
            padding: 1rem;
            border-bottom: 1px solid #eee;
            transition: background-color 0.3s ease;
        }
        .lesson-item:hover, .test-item:hover {
            background-color: #f8f9fa;
        }
        .lesson-item.completed {
            background-color: #d4edda;
            border-left: 4px solid #28a745;
        }
        .test-item.completed {
            background-color: #cce7ff;
            border-left: 4px solid #007bff;
        }
        .content-viewer {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <!-- Course Header -->
    <div class="course-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <a href="{{ route('user.dashboard') }}" class="btn btn-outline-light mb-3">
                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                    </a>
                    <h1>{{ $course->name }}</h1>
                    <p class="mb-0">{{ $course->description }}</p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="bg-white text-dark rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <span class="h4 mb-0">{{ $courseProgress }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid py-4">
        <div class="row">
            <!-- Course Content Sidebar -->
            <div class="col-md-4">
                <div class="sticky-top" style="top: 20px;">
                    <h4 class="mb-3">Course Content</h4>
                    @foreach($course->topics as $topicIndex => $topic)
                    <div class="topic-card">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-folder me-2"></i>{{ $topic->order }}. {{ $topic->title }}
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <!-- Lessons -->
                            @foreach($topic->lessons as $lesson)
                            @php
                                $isCompleted = $user->completedLessons()->where('lesson_id', $lesson->id)->exists();
                            @endphp
                            <div class="lesson-item {{ $isCompleted ? 'completed' : '' }}" 
                                 onclick="loadLesson({{ $lesson->id }})" style="cursor: pointer;">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-{{ $isCompleted ? 'check-circle text-success' : 'play-circle text-muted' }} me-2"></i>
                                    <span class="flex-grow-1">{{ $lesson->title }}</span>
                                    @if($lesson->duration)
                                    <small class="text-muted">{{ $lesson->duration }}min</small>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                            
                            <!-- MCQ Tests -->
                            @foreach($topic->mcqTests as $test)
                            @php
                                $testResult = $user->mcqResults()->where('mcq_test_id', $test->id)->first();
                            @endphp
                            <div class="test-item {{ $testResult ? 'completed' : '' }}" 
                                 onclick="loadTest({{ $test->id }})" style="cursor: pointer;">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-{{ $testResult ? 'trophy text-warning' : 'question-circle text-muted' }} me-2"></i>
                                    <span class="flex-grow-1">{{ $test->title }}</span>
                                    @if($testResult)
                                    <small class="text-success">{{ $testResult->percentage }}%</small>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="col-md-8">
                <div class="content-viewer" id="contentViewer">
                    <div class="text-center py-5">
                        <i class="fas fa-play fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">Select a lesson or test to begin</h4>
                        <p class="text-muted">Choose from the course content on the left to start learning.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function loadLesson(lessonId) {
            fetch(`/user/lesson/${lessonId}`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('contentViewer').innerHTML = html;
                });
        }

        function loadTest(testId) {
            window.location.href = `/user/mcq-test/${testId}`;
        }

        function markLessonComplete(lessonId) {
            fetch(`/user/lesson/${lessonId}/complete`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    location.reload();
                }
            });
        }
    </script>
</body>
</html>
