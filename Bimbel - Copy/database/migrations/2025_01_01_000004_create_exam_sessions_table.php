<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('exam_sessions')) {
            Schema::create('exam_sessions', function (Blueprint $table) {
                $table->id();
                $table->string('exam_name', 150);
                $table->integer('duration_minutes')->default(90);
                $table->integer('total_questions')->nullable();
                $table->integer('passing_score')->default(70);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_sessions');
    }
};
