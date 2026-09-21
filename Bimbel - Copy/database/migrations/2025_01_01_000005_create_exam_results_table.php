<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('exam_results')) {
            Schema::create('exam_results', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('exam_id')->constrained('exam_sessions')->onDelete('cascade');
                $table->integer('score')->nullable();
                $table->integer('correct_answers')->nullable();
                $table->integer('duration_spent')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamp('created_at')->useCurrent();

                $table->index('user_id');
                $table->index('exam_id');
                $table->index('completed_at');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_results');
    }
};
