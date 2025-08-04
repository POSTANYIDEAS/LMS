@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">{{ $course->name }} - Course Details</h1>
    <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">Back to Courses</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<!-- Course Info -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        @if($course->image)
                            <img src="{{ asset($course->image) }}" alt="{{ $course->name }}" class="img-fluid rounded">
                        @endif
                    </div>
                    <div class="col-md-9">
                        <h3>{{ $course->name }}</h3>
                        <p>{{ $course->description }}</p>
                        <p><strong>Price:</strong> ${{ number_format($course->price, 2) }}</p>
                        <p><strong>Status:</strong> 
                            <span class="badge bg-{{ $course->status == 'active' ? 'success' : 'secondary' }}">
                                {{ ucfirst($course->status) }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Topic Form -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Add New Topic</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.topics.store', $course) }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" name="title" class="form-control mb-2" placeholder="Topic Title" required>
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="order" class="form-control mb-2" placeholder="Order" value="{{ $course->topics->count() + 1 }}" required>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">Add Topic</button>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <label class="form-label">Topic Description</label>
                            <textarea name="description" class="form-control" placeholder="Enter topic description..." rows="4"></textarea>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Topics and Lessons -->
@foreach($course->topics as $topic)
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>{{ $topic->order }}. {{ $topic->title }}</h5>
                <div>
                    <button class="btn btn-sm btn-warning" data-bs-toggle="collapse" data-bs-target="#editTopic{{ $topic->id }}">Edit Topic</button>
                    <button class="btn btn-sm btn-success" data-bs-toggle="collapse" data-bs-target="#addLesson{{ $topic->id }}">Add Lesson</button>
                    <form action="{{ route('admin.topics.destroy', $topic) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete Topic</button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                @if($topic->description)
                    <div class="mb-3 p-3 bg-light rounded">
                        {!! $topic->description !!}
                    </div>
                @endif
                
                <!-- Edit Topic Form -->
                <div class="collapse mb-3" id="editTopic{{ $topic->id }}">
                    <form action="{{ route('admin.topics.update', $topic) }}" method="POST" class="border p-3 rounded bg-warning bg-opacity-10">
                        @csrf
                        @method('PUT')
                        <h6>Edit Topic</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" name="title" class="form-control mb-2" placeholder="Topic Title" value="{{ $topic->title }}" required>
                            </div>
                            <div class="col-md-3">
                                <input type="number" name="order" class="form-control mb-2" placeholder="Order" value="{{ $topic->order }}" required>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-warning mb-2">Update Topic</button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label class="form-label">Topic Description</label>
                                <textarea name="description" class="form-control rich-editor" rows="4">{{ $topic->description }}</textarea>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Add Lesson Form -->
                <div class="collapse mb-3" id="addLesson{{ $topic->id }}">
                    <form action="{{ route('admin.lessons.store', $topic) }}" method="POST" class="border p-3 rounded">
                        @csrf
                        <h6>Add New Lesson</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" name="title" class="form-control mb-2" placeholder="Lesson Title" required>
                            </div>
                            <div class="col-md-3">
                                <input type="url" name="video_url" class="form-control mb-2" placeholder="Video URL (Optional)">
                            </div>
                            <div class="col-md-3">
                                <input type="number" name="duration" class="form-control mb-2" placeholder="Duration (min)">
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="order" class="form-control mb-2" placeholder="Order" value="{{ $topic->lessons->count() + 1 }}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-10">
                                <label class="form-label">Lesson Description</label>
                                <textarea name="description" class="form-control rich-editor" placeholder="Enter lesson description with rich formatting..." rows="4" required></textarea>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary mt-4">Add Lesson</button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Lessons -->
                @foreach($topic->lessons as $lesson)
                <div class="border rounded p-3 mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6>{{ $lesson->order }}. {{ $lesson->title }}</h6>
                        <div>
                            <button class="btn btn-sm btn-info" data-bs-toggle="collapse" data-bs-target="#addContent{{ $lesson->id }}">Add Content</button>
                            <button class="btn btn-sm btn-secondary" data-bs-toggle="collapse" data-bs-target="#addMcqTest{{ $lesson->id }}">Add MCQ Test</button>
                            <button class="btn btn-sm btn-warning" data-bs-toggle="collapse" data-bs-target="#editLesson{{ $lesson->id }}">Edit</button>
                            <form action="{{ route('admin.lessons.destroy', $lesson) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Lesson Description -->
                    <div class="mb-3 p-3 bg-light rounded">
                        {!! $lesson->description !!}
                    </div>
                    
                    @if($lesson->video_url)
                        <p><strong>Video:</strong> <a href="{{ $lesson->video_url }}" target="_blank">{{ $lesson->video_url }}</a></p>
                    @endif
                    @if($lesson->duration)
                        <p><strong>Duration:</strong> {{ $lesson->duration }} minutes</p>
                    @endif

                    <!-- Edit Lesson Form -->
                    <div class="collapse mb-3" id="editLesson{{ $lesson->id }}">
                        <form action="{{ route('admin.lessons.update', $lesson) }}" method="POST" class="border p-3 rounded bg-info bg-opacity-10">
                            @csrf
                            @method('PUT')
                            <h6>Edit Lesson</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="text" name="title" class="form-control mb-2" placeholder="Lesson Title" value="{{ $lesson->title }}" required>
                                </div>
                                <div class="col-md-3">
                                    <input type="url" name="video_url" class="form-control mb-2" placeholder="Video URL" value="{{ $lesson->video_url }}">
                                </div>
                                <div class="col-md-3">
                                    <input type="number" name="duration" class="form-control mb-2" placeholder="Duration (min)" value="{{ $lesson->duration }}">
                                </div>
                                <div class="col-md-2">
                                    <input type="number" name="order" class="form-control mb-2" placeholder="Order" value="{{ $lesson->order }}" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-10">
                                    <label class="form-label">Lesson Description</label>
                                    <textarea name="description" class="form-control rich-editor" rows="4" required>{{ $lesson->description }}</textarea>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-info mt-4">Update Lesson</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Add Content Form -->
                    <div class="collapse mb-3" id="addContent{{ $lesson->id }}">
                        <form action="{{ route('admin.lesson-content.store', $lesson) }}" method="POST" enctype="multipart/form-data" class="border p-3 rounded bg-light">
                            @csrf
                            <h6>Add Content to Lesson</h6>
                            <div class="row">
                                <div class="col-md-3">
                                    <select name="type" class="form-control mb-2" required onchange="toggleContentFields(this, {{ $lesson->id }})">
                                        <option value="">Select Content Type</option>
                                        <option value="text">Text/Rich Content</option>
                                        <option value="notepad">Notepad/Plain Text</option>
                                        <option value="image">Image</option>
                                        <option value="video">Video File</option>
                                        <option value="youtube">YouTube Link</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="title" class="form-control mb-2" placeholder="Content Title">
                                </div>
                                <div class="col-md-2">
                                    <input type="number" name="order" class="form-control mb-2" placeholder="Order" value="{{ $lesson->contents->count() + 1 }}" required>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-success mb-2 w-100">Add Content</button>
                                </div>
                            </div>
                            
                            <!-- Text/Notepad Field -->
                            <div id="textField{{ $lesson->id }}" style="display:none;">
                                <label class="form-label">Text Content (Rich Text Editor)</label>
                                <textarea name="content" class="form-control rich-editor" placeholder="Enter your text content with formatting..." rows="6"></textarea>
                            </div>
                            
                            <!-- Other fields remain the same -->
                            <div id="imageField{{ $lesson->id }}" style="display:none;">
                                <label class="form-label">Upload Image</label>
                                <input type="file" name="image" class="form-control mb-2" accept="image/*">
                                <small class="text-muted">Supported formats: JPEG, PNG, JPG, GIF (Max: 2MB)</small>
                            </div>
                            
                            <div id="videoField{{ $lesson->id }}" style="display:none;">
                                <label class="form-label">Upload Video File</label>
                                <input type="file" name="video" class="form-control mb-2" accept="video/*">
                                <small class="text-muted">Supported formats: MP4, AVI, MOV (Max: 50MB)</small>
                            </div>
                            
                            <div id="youtubeField{{ $lesson->id }}" style="display:none;">
                                <label class="form-label">YouTube URL</label>
                                <input type="url" name="content" class="form-control mb-2" placeholder="https://www.youtube.com/watch?v=...">
                                <small class="text-muted">Paste the full YouTube video URL</small>
                            </div>
                            
                            <div id="notepadField{{ $lesson->id }}" style="display:none;">
                                <label class="form-label">Notepad Content</label>
                                <div class="notepad-container">
                                    <div class="notepad-toolbar">
                                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatNotepad('bold', {{ $lesson->id }})"><b>B</b></button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatNotepad('italic', {{ $lesson->id }})"><i>I</i></button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatNotepad('underline', {{ $lesson->id }})"><u>U</u></button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatNotepad('insertUnorderedList', {{ $lesson->id }})">• List</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatNotepad('insertOrderedList', {{ $lesson->id }})">1. List</button>
                                        <select class="form-select form-select-sm d-inline-block w-auto" onchange="changeFontSize(this.value, {{ $lesson->id }})">
                                            <option value="14px">14px</option>
                                            <option value="16px">16px</option>
                                            <option value="18px">18px</option>
                                            <option value="20px">20px</option>
                                        </select>
                                    </div>
                                    <div class="notepad-editor" 
                                         id="notepadEditor{{ $lesson->id }}" 
                                         contenteditable="true" 
                                         data-lesson="{{ $lesson->id }}"
                                         style="min-height: 300px; padding: 20px; border: 1px solid #ddd; background: white; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 14px; line-height: 1.6; outline: none;">
                                    </div>
                                    <textarea name="content" id="notepadContent{{ $lesson->id }}" style="display: none;"></textarea>
                                </div>
                            </div>

                            <div class="mt-2">
                                <label class="form-label">Content Description (Optional)</label>
                                <textarea name="description" class="form-control rich-editor" placeholder="Brief description about this content..." rows="2"></textarea>
                            </div>
                        </form>
                    </div>

                    <!-- Lesson Contents Display -->
                    @foreach($lesson->contents as $content)
                    <div class="border rounded p-3 mb-2 bg-light">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <strong>{{ $content->order }}. {{ $content->title ?: ucfirst($content->type) . ' Content' }}</strong>
                                <span class="badge bg-secondary ms-2">{{ ucfirst($content->type) }}</span>
                            </div>
                            <form action="{{ route('admin.lesson-content.destroy', $content) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </div>
                        
                        @if($content->description)
                            <div class="text-muted mb-2 p-2 bg-white rounded"><em>{!! $content->description !!}</em></div>
                        @endif
                        
                        @if($content->type === 'text')
                            <div class="p-3 bg-white rounded border">
                                {!! $content->content !!}
                            </div>
                        @elseif($content->type === 'notepad')
                            <div class="notepad-display" style="background: white; border: 1px solid #ddd; border-radius: 8px; padding: 20px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                {!! $content->content !!}
                            </div>
                        @elseif($content->type === 'image')
                            <img src="{{ asset($content->content) }}" alt="{{ $content->title }}" class="img-fluid rounded">
                        @elseif($content->type === 'video')
                            <video controls class="w-100" style="max-height: 400px;">
                                <source src="{{ asset($content->content) }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        @elseif($content->type === 'youtube')
                            @php
                                $videoId = '';
                                if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\n?#]+)/', $content->content, $matches)) {
                                    $videoId = $matches[1];
                                }
                            @endphp
                            @if($videoId)
                                <iframe width="100%" height="315" src="https://www.youtube.com/embed/{{ $videoId }}" frameborder="0" allowfullscreen></iframe>
                            @else
                                <p class="text-danger">Invalid YouTube URL</p>
                            @endif
                        @endif
                    </div>
                    @endforeach

                    <!-- Add MCQ Test Form -->
                    <div class="collapse mb-3" id="addMcqTest{{ $lesson->id }}">
                        <form action="{{ route('admin.mcq-tests.store', $lesson) }}" method="POST" class="border p-3 rounded bg-warning">
                            @csrf
                            <h6>Add MCQ Test to Lesson</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="text" name="title" class="form-control mb-2" placeholder="Test Title" required>
                                </div>
                                <div class="col-md-3">
                                    <input type="number" name="time_limit" class="form-control mb-2" placeholder="Time Limit (min)">
                                </div>
                                <div class="col-md-3">
                                    <input type="number" name="pass_percentage" class="form-control mb-2" placeholder="Pass %" value="60" required>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" name="order" class="form-control mb-2" placeholder="Order" value="{{ ($lesson->mcqTests ? $lesson->mcqTests->count() : 0) + 1 }}" required>
                                </div>
                            </div>
                            <textarea name="description" class="form-control mb-2" placeholder="Test Description"></textarea>
                            <button type="submit" class="btn btn-warning">Add MCQ Test</button>
                        </form>
                    </div>

                    <!-- MCQ Tests -->
                    @if($lesson->mcqTests)
                        @foreach($lesson->mcqTests as $test)
                        <div class="border rounded p-3 mb-2 bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6>📝 {{ $test->title }} ({{ $test->questions->count() }} questions)</h6>
                                <div>
                                    <button class="btn btn-sm btn-info" data-bs-toggle="collapse" data-bs-target="#addQuestion{{ $test->id }}">Add Question</button>
                                    <form action="{{ route('admin.mcq-tests.destroy', $test) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete test?')">Delete</button>
                                    </form>
                                </div>
                            </div>
                            
                            <!-- Add Question Form -->
                            <div class="collapse mt-3" id="addQuestion{{ $test->id }}">
                                <form action="{{ route('admin.mcq-questions.store', $test) }}" method="POST" class="border p-3 rounded">
                                    @csrf
                                    <div class="mb-2">
                                        <textarea name="question" class="form-control" placeholder="Question" required></textarea>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="text" name="option_a" class="form-control mb-2" placeholder="Option A" required>
                                            <input type="text" name="option_b" class="form-control mb-2" placeholder="Option B" required>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" name="option_c" class="form-control mb-2" placeholder="Option C" required>
                                            <input type="text" name="option_d" class="form-control mb-2" placeholder="Option D" required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <select name="correct_answer" class="form-control mb-2" required>
                                                <option value="">Correct Answer</option>
                                                <option value="A">A</option>
                                                <option value="B">B</option>
                                                <option value="C">C</option>
                                                <option value="D">D</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" name="explanation" class="form-control mb-2" placeholder="Explanation (optional)">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="number" name="order" class="form-control mb-2" value="{{ $test->questions->count() + 1 }}" required>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-success">Add Question</button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endforeach

<script>
// Simple solution - remove TinyMCE and use basic textarea
document.addEventListener('DOMContentLoaded', function() {
    // Remove any TinyMCE instances
    if (typeof tinymce !== 'undefined') {
        tinymce.remove();
    }
    
    // Close all collapse elements on page load
    const collapseElements = document.querySelectorAll('.collapse');
    collapseElements.forEach(element => {
        element.classList.remove('show');
    });
});

function toggleContentFields(select, lessonId) {
    // Hide all fields first
    const fields = ['textField', 'notepadField', 'imageField', 'videoField', 'youtubeField'];
    fields.forEach(field => {
        const element = document.getElementById(field + lessonId);
        if (element) {
            element.style.display = 'none';
        }
    });
    
    // Show selected field
    if (select.value) {
        const selectedField = document.getElementById(select.value + 'Field' + lessonId);
        if (selectedField) {
            selectedField.style.display = 'block';
        }
    }
}

function formatNotepad(command, lessonId) {
    const editor = document.getElementById('notepadEditor' + lessonId);
    editor.focus();
    document.execCommand(command, false, null);
    updateNotepadContent(lessonId);
}

function changeFontSize(size, lessonId) {
    const editor = document.getElementById('notepadEditor' + lessonId);
    editor.style.fontSize = size;
    updateNotepadContent(lessonId);
}

function updateNotepadContent(lessonId) {
    const editor = document.getElementById('notepadEditor' + lessonId);
    const textarea = document.getElementById('notepadContent' + lessonId);
    textarea.value = editor.innerHTML;
}

// Initialize notepad editors
document.addEventListener('DOMContentLoaded', function() {
    const notepadEditors = document.querySelectorAll('.notepad-editor');
    
    notepadEditors.forEach(editor => {
        const lessonId = editor.dataset.lesson;
        
        // Update content on input
        editor.addEventListener('input', function() {
            updateNotepadContent(lessonId);
        });
        
        // Handle paste to clean up formatting
        editor.addEventListener('paste', function(e) {
            e.preventDefault();
            const text = e.clipboardData.getData('text/plain');
            document.execCommand('insertText', false, text);
            updateNotepadContent(lessonId);
        });
        
        // Add placeholder behavior
        if (editor.innerHTML.trim() === '') {
            editor.innerHTML = '<p>Start typing your content here...</p>';
            editor.style.color = '#999';
        }
        
        editor.addEventListener('focus', function() {
            if (editor.innerHTML === '<p>Start typing your content here...</p>') {
                editor.innerHTML = '';
                editor.style.color = '#333';
            }
        });
        
        editor.addEventListener('blur', function() {
            if (editor.innerHTML.trim() === '' || editor.innerHTML === '<p><br></p>') {
                editor.innerHTML = '<p>Start typing your content here...</p>';
                editor.style.color = '#999';
            }
            updateNotepadContent(lessonId);
        });
    });
});
</script>
@endsection






