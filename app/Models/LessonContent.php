<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'type',
        'content',
        'title',
        'description',
        'order'
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
