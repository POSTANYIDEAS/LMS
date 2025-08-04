<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // Relationship with allocated courses
    public function allocatedCourses()
    {
        return $this->belongsToMany(Course::class, 'user_courses', 'user_id', 'course_id')
                    ->withPivot('allocated_at', 'allocated_by', 'completed_at')
                    ->withTimestamps();
    }

    // Relationship with completed lessons
    public function completedLessons()
    {
        return $this->belongsToMany(Lesson::class, 'user_lesson_completions', 'user_id', 'lesson_id')
                    ->withPivot('completed_at')
                    ->withTimestamps();
    }

    // Relationship with MCQ test results
    public function mcqResults()
    {
        return $this->hasMany(McqResult::class);
    }
}


