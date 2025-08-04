<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class McqResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'mcq_test_id',
        'score',
        'total_questions',
        'percentage',
        'answers',
        'completed_at'
    ];

    protected $casts = [
        'answers' => 'array',
        'percentage' => 'decimal:2',
        'completed_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mcqTest()
    {
        return $this->belongsTo(McqTest::class);
    }
}
