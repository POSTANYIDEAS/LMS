<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lesson_contents', function (Blueprint $table) {
            $table->dropColumn('type');
        });
        
        Schema::table('lesson_contents', function (Blueprint $table) {
            $table->enum('type', ['text', 'image', 'video', 'youtube', 'notepad'])->after('lesson_id');
        });
    }

    public function down(): void
    {
        Schema::table('lesson_contents', function (Blueprint $table) {
            $table->dropColumn('type');
        });
        
        Schema::table('lesson_contents', function (Blueprint $table) {
            $table->enum('type', ['text', 'image', 'video', 'youtube'])->after('lesson_id');
        });
    }
};
