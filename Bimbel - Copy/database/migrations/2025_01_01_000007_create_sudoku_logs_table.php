<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('sudoku_logs')) {
            Schema::create('sudoku_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->integer('score')->nullable();
                $table->integer('duration_seconds')->nullable();
                $table->timestamp('completed_at')->useCurrent();

                $table->index('user_id');
                $table->index('completed_at');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sudoku_logs');
    }
};
