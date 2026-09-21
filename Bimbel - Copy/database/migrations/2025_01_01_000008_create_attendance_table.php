<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('attendance')) {
            Schema::create('attendance', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->date('attendance_date');
                $table->enum('status', ['hadir', 'absen', 'izin'])->default('absen');
                $table->timestamp('created_at')->useCurrent();

                $table->unique(['user_id', 'attendance_date'], 'unique_attendance');
                $table->index('user_id');
                $table->index('attendance_date');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};
