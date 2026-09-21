<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('student_monthly_scores')) {
            Schema::create('student_monthly_scores', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('student_name_custom', 150)->nullable();
                $table->integer('period_year');
                $table->integer('period_month');
                $table->date('recorded_date');
                $table->decimal('twk', 6, 2)->default(0);
                $table->decimal('tiu', 6, 2)->default(0);
                $table->decimal('tkp', 6, 2)->default(0);
                $table->decimal('nilai_cat', 6, 2)->default(0);
                $table->integer('lari_meters')->default(0);
                $table->integer('push_up')->default(0);
                $table->integer('sit_up')->default(0);
                $table->integer('pull_up')->default(0);
                $table->decimal('shuttle_seconds', 5, 2)->default(0);
                $table->decimal('renang_seconds', 5, 2)->default(0);
                $table->integer('renang_distance')->default(25);
                $table->decimal('total_samapta', 6, 2)->default(0);
                $table->decimal('nilai_total', 6, 2)->default(0);
                $table->timestamps();

                $table->index(['period_year', 'period_month'], 'idx_period');
                $table->index(['user_id', 'period_year', 'period_month'], 'idx_user_period');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('student_monthly_scores');
    }
};
