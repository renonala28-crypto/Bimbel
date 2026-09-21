<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('physical_performance')) {
            Schema::create('physical_performance', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->decimal('lari_distance_km', 5, 2)->nullable();
                $table->integer('lari_time_minutes')->nullable();
                $table->decimal('renang_distance_m', 5, 2)->nullable();
                $table->integer('renang_time_minutes')->nullable();
                $table->date('recorded_date')->nullable();
                $table->timestamp('created_at')->useCurrent();

                $table->index('user_id');
                $table->index('recorded_date');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('physical_performance');
    }
};
