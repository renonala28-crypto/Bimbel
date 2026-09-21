<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->integer('amount')->default(100000);
                $table->string('proof_file', 255);
                $table->timestamp('verified_at')->nullable();
                $table->timestamp('created_at')->useCurrent();

                $table->index('user_id');
                $table->index('verified_at');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
