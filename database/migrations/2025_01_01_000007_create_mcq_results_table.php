<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mcq_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('mcq_test_id')->constrained()->onDelete('cascade');
            $table->integer('score');
            $table->integer('total_questions');
            $table->decimal('percentage', 5, 2);
            $table->json('answers');
            $table->timestamp('completed_at');
            $table->timestamps();
            
            $table->unique(['user_id', 'mcq_test_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mcq_results');
    }
};
