<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Course;
use App\Models\Topic;
use App\Models\Lesson;
use App\Models\LessonContent;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function showLogin()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $admin = Admin::where('email', $credentials['email'])->first();

        if ($admin && Hash::check($credentials['password'], $admin->password)) {
            session(['admin_id' => $admin->id]);
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    public function dashboard()
    {
        $admin = Admin::find(session('admin_id'));
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        return view('admin.dashboard', compact('admin'));
    }

    public function courses()
    {
        $courses = Course::latest()->get();
        return view('admin.courses.index', compact('courses'));
    }

    public function createCourse()
    {
        return view('admin.courses.create');
    }

    public function storeCourse(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive'
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('images/courses'), $imageName);
            $data['image'] = 'images/courses/'.$imageName;
        }

        Course::create($data);

        return redirect()->route('admin.courses.index')->with('success', 'Course created successfully!');
    }

    public function editCourse(Course $course)
    {
        return view('admin.courses.edit', compact('course'));
    }

    public function updateCourse(Request $request, Course $course)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive'
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('images/courses'), $imageName);
            $data['image'] = 'images/courses/'.$imageName;
        }

        $course->update($data);

        return redirect()->route('admin.courses.index')->with('success', 'Course updated successfully!');
    }

    public function deleteCourse(Course $course)
    {
        $course->delete();
        return redirect()->route('admin.courses.index')->with('success', 'Course deleted successfully!');
    }

    public function courseDetails(Course $course)
    {
        $course->load([
            'topics.lessons.contents',
            'topics.lessons.mcqTests.questions'
        ]);
        
        return view('admin.courses.details', compact('course'));
    }

    public function storeTopic(Request $request, Course $course)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:0'
        ]);

        $course->topics()->create($request->all());
        return redirect()->route('admin.courses.details', $course)->with('success', 'Topic added successfully!');
    }

    public function updateTopic(Request $request, Topic $topic)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:0'
        ]);

        $topic->update($request->all());
        return redirect()->route('admin.courses.details', $topic->course)->with('success', 'Topic updated successfully!');
    }

    public function deleteTopic(Topic $topic)
    {
        $course = $topic->course;
        $topic->delete();
        return redirect()->route('admin.courses.details', $course)->with('success', 'Topic deleted successfully!');
    }

    public function storeLesson(Request $request, Topic $topic)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'video_url' => 'nullable|url',
            'duration' => 'nullable|integer|min:0',
            'order' => 'required|integer|min:0'
        ]);

        $topic->lessons()->create($request->all());
        return redirect()->route('admin.courses.details', $topic->course)->with('success', 'Lesson added successfully!');
    }

    public function editLesson(Lesson $lesson)
    {
        return view('admin.lessons.edit', compact('lesson'));
    }

    public function updateLesson(Request $request, Lesson $lesson)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'video_url' => 'nullable|url',
            'duration' => 'nullable|integer|min:0',
            'order' => 'required|integer|min:0'
        ]);

        $lesson->update($request->all());
        return redirect()->route('admin.courses.details', $lesson->topic->course)->with('success', 'Lesson updated successfully!');
    }

    public function deleteLesson(Lesson $lesson)
    {
        $course = $lesson->topic->course;
        $lesson->delete();
        return redirect()->route('admin.courses.details', $course)->with('success', 'Lesson deleted successfully!');
    }

    public function storeLessonContent(Request $request, Lesson $lesson)
    {
        $rules = [
            'type' => 'required|in:text,image,video,youtube,notepad',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:0'
        ];

        // Add conditional validation based on type
        if ($request->type === 'text' || $request->type === 'youtube' || $request->type === 'notepad') {
            $rules['content'] = 'required|string';
        } elseif ($request->type === 'image') {
            $rules['image'] = 'required|image|mimes:jpeg,png,jpg,gif|max:2048';
        } elseif ($request->type === 'video') {
            $rules['video'] = 'required|mimes:mp4,avi,mov|max:50000';
        }

        $request->validate($rules);

        $data = $request->all();

        // Handle file uploads
        if ($request->type === 'image' && $request->hasFile('image')) {
            if (!file_exists(public_path('images/lessons'))) {
                mkdir(public_path('images/lessons'), 0777, true);
            }
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('images/lessons'), $imageName);
            $data['content'] = 'images/lessons/'.$imageName;
        } elseif ($request->type === 'video' && $request->hasFile('video')) {
            if (!file_exists(public_path('videos/lessons'))) {
                mkdir(public_path('videos/lessons'), 0777, true);
            }
            $videoName = time().'.'.$request->video->extension();
            $request->video->move(public_path('videos/lessons'), $videoName);
            $data['content'] = 'videos/lessons/'.$videoName;
        }

        $lesson->contents()->create($data);
        return redirect()->route('admin.courses.details', $lesson->topic->course)->with('success', 'Content added successfully!');
    }

    public function deleteLessonContent(LessonContent $content)
    {
        $course = $content->lesson->topic->course;
        $content->delete();
        return redirect()->route('admin.courses.details', $course)->with('success', 'Content deleted successfully!');
    }

    public function logout()
    {
        session()->forget('admin_id');
        return redirect()->route('admin.login');
    }

    public function users()
    {
        $admin = Admin::find(session('admin_id'));
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        $users = User::with('allocatedCourses')->latest()->get();
        $courses = Course::where('status', 'active')->get();
        return view('admin.users.index', compact('users', 'admin', 'courses'));
    }

    public function createUser()
    {
        return view('admin.users.create');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'required|string|unique:users',
            'password' => 'required|string|min:6',
            'is_active' => 'boolean'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully!');
    }

    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'required|string|unique:users,phone,' . $user->id,
            'password' => 'nullable|string|min:6',
            'is_active' => 'boolean'
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'is_active' => $request->has('is_active')
        ];

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully!');
    }

    public function deleteUser(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully!');
    }

    public function toggleUserStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'activated' : 'deactivated';
        return redirect()->route('admin.users.index')->with('success', "User {$status} successfully!");
    }

    public function allocateCourse(Request $request, User $user)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id'
        ]);

        $user->allocatedCourses()->syncWithoutDetaching([$request->course_id => [
            'allocated_at' => now(),
            'allocated_by' => session('admin_id')
        ]]);

        return redirect()->route('admin.users.index')->with('success', 'Course allocated successfully!');
    }

    public function deallocateCourse(User $user, $courseId)
    {
        $user->allocatedCourses()->detach($courseId);
        return redirect()->route('admin.users.index')->with('success', 'Course deallocated successfully!');
    }

    public function userCourseDetails(User $user)
    {
        $user->load(['allocatedCourses.topics.lessons', 'allocatedCourses.topics.mcqTests', 'completedLessons', 'mcqResults']);
        return view('admin.users.course-details', compact('user'));
    }

    // MCQ Test methods
    public function storeMcqTest(Request $request, Lesson $lesson)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'time_limit' => 'nullable|integer|min:1',
            'pass_percentage' => 'required|numeric|min:0|max:100',
            'order' => 'required|integer|min:0'
        ]);

        $data = $request->all();
        $data['topic_id'] = $lesson->topic_id;

        $lesson->mcqTests()->create($data);
        
        return redirect()->route('admin.courses.details', $lesson->topic->course)->with('success', 'MCQ Test added successfully!');
    }

    public function storeMcqQuestion(Request $request, McqTest $test)
    {
        $request->validate([
            'question' => 'required|string',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'required|string',
            'option_d' => 'required|string',
            'correct_answer' => 'required|in:A,B,C,D',
            'explanation' => 'nullable|string',
            'order' => 'required|integer|min:0'
        ]);

        $test->questions()->create($request->all());
        return redirect()->route('admin.courses.details', $test->lesson->topic->course)->with('success', 'Question added successfully!');
    }

    public function deleteMcqTest(McqTest $test)
    {
        $course = $test->lesson->topic->course;
        $test->delete();
        return redirect()->route('admin.courses.details', $course)->with('success', 'MCQ Test deleted successfully!');
    }

    public function deleteMcqQuestion(McqQuestion $question)
    {
        $course = $question->mcqTest->lesson->topic->course;
        $question->delete();
        return redirect()->route('admin.courses.details', $course)->with('success', 'Question deleted successfully!');
    }
}
