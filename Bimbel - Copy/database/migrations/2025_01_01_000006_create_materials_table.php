<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('materials')) {
            Schema::create('materials', function (Blueprint $table) {
                $table->id();
                $table->string('category', 50);
                $table->string('title', 150);
                $table->string('file_path', 255);
                $table->integer('file_size')->nullable();
                $table->foreignId('uploaded_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamp('uploaded_at')->useCurrent();

                $table->index('category');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
