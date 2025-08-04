<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic_id',
        'title',
        'description',
        'video_url',
        'duration',
        'order'
    ];

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function contents()
    {
        return $this->hasMany(LessonContent::class)->orderBy('order');
    }

    public function mcqTests()
    {
        return $this->hasManyThrough(McqTest::class, Topic::class, 'id', 'topic_id', 'topic_id', 'id')->orderBy('order');
    }
}



