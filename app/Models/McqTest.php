<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class McqTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic_id',
        'lesson_id',
        'title',
        'description',
        'time_limit',
        'total_questions',
        'pass_percentage',
        'is_active',
        'order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'pass_percentage' => 'decimal:2'
    ];

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function questions()
    {
        return $this->hasMany(McqQuestion::class)->orderBy('order');
    }

    public function results()
    {
        return $this->hasMany(McqResult::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}





