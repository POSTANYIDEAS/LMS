<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController; // ✅ Added
use App\Http\Controllers\UserController; // ✅ Added

// Admin login routes
Route::get('admin/login', [AdminController::class, 'showLogin'])->name('admin.login');
Route::post('admin/login', [AdminController::class, 'login'])->name('admin.login.submit');

// Protected admin routes
Route::middleware('adminAuth')->group(function () {
    Route::get('admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Course CRUD routes
    Route::get('admin/courses', [AdminController::class, 'courses'])->name('admin.courses.index');
    Route::get('admin/courses/create', [AdminController::class, 'createCourse'])->name('admin.courses.create');
    Route::post('admin/courses', [AdminController::class, 'storeCourse'])->name('admin.courses.store');
    Route::get('admin/courses/{course}/edit', [AdminController::class, 'editCourse'])->name('admin.courses.edit');
    Route::put('admin/courses/{course}', [AdminController::class, 'updateCourse'])->name('admin.courses.update');
    Route::delete('admin/courses/{course}', [AdminController::class, 'deleteCourse'])->name('admin.courses.destroy');
    Route::get('admin/courses/{course}/details', [AdminController::class, 'courseDetails'])->name('admin.courses.details');
    
    // Topic routes
    Route::post('admin/courses/{course}/topics', [AdminController::class, 'storeTopic'])->name('admin.topics.store');
    Route::put('admin/topics/{topic}', [AdminController::class, 'updateTopic'])->name('admin.topics.update');
    Route::delete('admin/topics/{topic}', [AdminController::class, 'deleteTopic'])->name('admin.topics.destroy');
    
    // User management routes
    Route::get('admin/users', [AdminController::class, 'users'])->name('admin.users.index');
    Route::get('admin/users/create', [AdminController::class, 'createUser'])->name('admin.users.create');
    Route::post('admin/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::get('admin/users/{user}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
    Route::put('admin/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('admin/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.users.destroy');
    Route::post('admin/users/{user}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('admin.users.toggle-status');
    Route::post('admin/users/{user}/allocate-course', [AdminController::class, 'allocateCourse'])->name('admin.users.allocate-course');
    Route::get('admin/users/{user}/deallocate-course/{course}', [AdminController::class, 'deallocateCourse'])->name('admin.users.deallocate-course');
    Route::get('admin/users/{user}/course-details', [AdminController::class, 'userCourseDetails'])->name('admin.users.course-details');
    
    Route::get('admin/logout', [AdminController::class, 'logout'])->name('admin.logout');
});

// User Dashboard Routes
Route::middleware('userAuth')->group(function () {
    Route::get('user/dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');
    Route::get('user/course/{course}', [UserController::class, 'viewCourse'])->name('user.course.view');
    Route::get('user/lesson/{lesson}', [UserController::class, 'viewLesson'])->name('user.lesson.view');
    Route::post('user/lesson/{lesson}/complete', [UserController::class, 'completeLesson'])->name('user.lesson.complete');
    Route::get('user/mcq-test/{test}', [UserController::class, 'takeMcqTest'])->name('user.mcq.test');
    Route::post('user/mcq-test/{test}/submit', [UserController::class, 'submitMcqTest'])->name('user.mcq.submit');
});

// Default welcome page
Route::get('/', function () {
    return view('welcome');
});

// Lesson routes
Route::post('admin/topics/{topic}/lessons', [AdminController::class, 'storeLesson'])->name('admin.lessons.store');
Route::get('admin/lessons/{lesson}/edit', [AdminController::class, 'editLesson'])->name('admin.lessons.edit');
Route::put('admin/lessons/{lesson}', [AdminController::class, 'updateLesson'])->name('admin.lessons.update');
Route::delete('admin/lessons/{lesson}', [AdminController::class, 'deleteLesson'])->name('admin.lessons.destroy');

// Lesson content routes
Route::post('admin/lessons/{lesson}/content', [AdminController::class, 'storeLessonContent'])->name('admin.lesson-content.store');
Route::delete('admin/lesson-content/{content}', [AdminController::class, 'deleteLessonContent'])->name('admin.lesson-content.destroy');

// MCQ Test routes
Route::post('admin/lessons/{lesson}/mcq-tests', [AdminController::class, 'storeMcqTest'])->name('admin.mcq-tests.store');
Route::delete('admin/mcq-tests/{test}', [AdminController::class, 'deleteMcqTest'])->name('admin.mcq-tests.destroy');

// MCQ Question routes  
Route::post('admin/mcq-tests/{test}/questions', [AdminController::class, 'storeMcqQuestion'])->name('admin.mcq-questions.store');
Route::delete('admin/mcq-questions/{question}', [AdminController::class, 'deleteMcqQuestion'])->name('admin.mcq-questions.destroy');

// User login routes
Route::get('user/login', [UserController::class, 'showLogin'])->name('user.login');
Route::post('user/login', [UserController::class, 'login'])->name('user.login.submit');
Route::get('user/logout', [UserController::class, 'logout'])->name('user.logout');



