<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'whatsapp_ortu')) {
                $table->string('whatsapp_ortu', 20)->nullable()->after('whatsapp');
            }
            if (!Schema::hasColumn('users', 'sekolah_asal')) {
                $table->string('sekolah_asal', 100)->nullable()->after('whatsapp_ortu');
            }
            if (!Schema::hasColumn('users', 'program_tujuan')) {
                $table->string('program_tujuan', 100)->nullable()->after('sekolah_asal');
            }
            if (!Schema::hasColumn('users', 'tempat_lahir')) {
                $table->string('tempat_lahir', 100)->nullable()->after('program_tujuan');
            }
            if (!Schema::hasColumn('users', 'tanggal_lahir')) {
                $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            }
            if (!Schema::hasColumn('users', 'nama_paket')) {
                $table->string('nama_paket', 100)->nullable()->after('tanggal_lahir');
            }
            if (!Schema::hasColumn('users', 'harga_paket')) {
                $table->decimal('harga_paket', 12, 2)->default(0.00)->after('nama_paket');
            }
            if (!Schema::hasColumn('users', 'tanggal_mulai')) {
                $table->date('tanggal_mulai')->nullable()->after('harga_paket');
            }
            if (!Schema::hasColumn('users', 'alamat')) {
                $table->text('alamat')->nullable()->after('tanggal_mulai');
            }
        });

        // Update status enum to include 'inactive'
        DB::statement("ALTER TABLE users MODIFY COLUMN status ENUM('pending', 'active', 'rejected', 'inactive') DEFAULT 'pending'");
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'whatsapp_ortu',
                'sekolah_asal',
                'program_tujuan',
                'tempat_lahir',
                'tanggal_lahir',
                'nama_paket',
                'harga_paket',
                'tanggal_mulai',
                'alamat',
            ]);
        });
    }
};
