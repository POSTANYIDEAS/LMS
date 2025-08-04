<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class McqQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'mcq_test_id',
        'question',
        'options',
        'correct_answer',
        'explanation',
        'order'
    ];

    protected $casts = [
        'options' => 'array'
    ];

    public function mcqTest()
    {
        return $this->belongsTo(McqTest::class);
    }
}
