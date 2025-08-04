<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\McqTest;

class UserController extends Controller
{
    public function showLogin()
    {
        return view('user.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'phone' => 'required|string',
            'password' => 'required',
        ]);

        $user = User::where('phone', $credentials['phone'])->where('is_active', true)->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            session(['user_id' => $user->id]);
            return redirect()->route('user.dashboard');
        }

        return back()->withErrors(['phone' => 'Invalid credentials or account is inactive']);
    }

    public function dashboard()
    {
        $user = User::find(session('user_id'));
        if (!$user) {
            return redirect()->route('user.login');
        }
        
        // Get allocated courses with progress
        $allocatedCourses = $user->allocatedCourses()->with(['topics.lessons', 'topics.mcqTests'])->get();
        
        // Calculate overall progress
        $totalProgress = 0;
        $courseCount = $allocatedCourses->count();
        
        foreach ($allocatedCourses as $course) {
            $totalProgress += $this->calculateCourseProgress($user, $course);
        }
        
        $overallProgress = $courseCount > 0 ? round($totalProgress / $courseCount, 2) : 0;
        
        return view('user.dashboard', compact('user', 'allocatedCourses', 'overallProgress'));
    }

    public function viewCourse($courseId)
    {
        $user = User::find(session('user_id'));
        $course = $user->allocatedCourses()->with(['topics.lessons.contents', 'topics.mcqTests.questions'])->findOrFail($courseId);
        
        $courseProgress = $this->calculateCourseProgress($user, $course);
        
        return view('user.course-view', compact('user', 'course', 'courseProgress'));
    }

    public function completeLesson(Request $request, $lessonId)
    {
        $user = User::find(session('user_id'));
        
        // Mark lesson as completed
        $user->completedLessons()->syncWithoutDetaching([$lessonId => ['completed_at' => now()]]);
        
        return response()->json(['success' => true]);
    }

    public function takeMcqTest($testId)
    {
        $user = User::find(session('user_id'));
        $test = McqTest::with('questions.options')->findOrFail($testId);
        
        return view('user.mcq-test', compact('user', 'test'));
    }

    public function submitMcqTest(Request $request, $testId)
    {
        $user = User::find(session('user_id'));
        $test = McqTest::with('questions.options')->findOrFail($testId);
        
        $answers = $request->input('answers', []);
        $score = 0;
        $totalQuestions = $test->questions->count();
        
        foreach ($test->questions as $question) {
            $selectedOption = $answers[$question->id] ?? null;
            if ($selectedOption && $question->options->where('id', $selectedOption)->where('is_correct', true)->count() > 0) {
                $score++;
            }
        }
        
        $percentage = $totalQuestions > 0 ? round(($score / $totalQuestions) * 100, 2) : 0;
        
        // Save test result
        $user->mcqResults()->updateOrCreate(
            ['mcq_test_id' => $testId],
            [
                'score' => $score,
                'total_questions' => $totalQuestions,
                'percentage' => $percentage,
                'answers' => json_encode($answers),
                'completed_at' => now()
            ]
        );
        
        return redirect()->route('user.course.view', $test->topic->course_id)
                       ->with('success', "Test completed! You scored {$score}/{$totalQuestions} ({$percentage}%)");
    }

    public function logout()
    {
        session()->forget('user_id');
        return redirect()->route('user.login');
    }

    private function calculateCourseProgress($user, $course)
    {
        $totalItems = 0;
        $completedItems = 0;
        
        foreach ($course->topics as $topic) {
            // Count lessons
            $totalItems += $topic->lessons->count();
            $completedItems += $user->completedLessons()->whereIn('lesson_id', $topic->lessons->pluck('id'))->count();
            
            // Count MCQ tests
            $totalItems += $topic->mcqTests->count();
            $completedItems += $user->mcqResults()->whereIn('mcq_test_id', $topic->mcqTests->pluck('id'))->count();
        }
        
        return $totalItems > 0 ? round(($completedItems / $totalItems) * 100, 2) : 0;
    }
}


