<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add 'sakit' to the attendance status enum
        DB::statement("ALTER TABLE attendance MODIFY COLUMN status ENUM('hadir', 'absen', 'izin', 'sakit') DEFAULT 'absen'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE attendance MODIFY COLUMN status ENUM('hadir', 'absen', 'izin') DEFAULT 'absen'");
    }
};
