<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('nickname', 50);
            $table->string('whatsapp', 20);
            $table->string('email', 100)->unique();
            $table->string('password');
            $table->string('remember_token', 255)->nullable();
            $table->enum('role', ['admin', 'siswa'])->default('siswa');
            $table->enum('status', ['pending', 'active', 'rejected'])->default('pending');
            $table->text('rejected_reason')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();

            $table->index('email');
            $table->index('role');
            $table->index('status');
            $table->index('remember_token');
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
