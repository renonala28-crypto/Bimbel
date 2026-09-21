<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'nickname',
        'whatsapp',
        'whatsapp_ortu',
        'sekolah_asal',
        'program_tujuan',
        'tempat_lahir',
        'tanggal_lahir',
        'nama_paket',
        'harga_paket',
        'tanggal_mulai',
        'alamat',
        'email',
        'password',
        'role',
        'status',
        'rejected_reason',
        'rejected_at',
        'remember_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'rejected_at'       => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // Relasi
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function examResults()
    {
        return $this->hasMany(ExamResult::class);
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    public function monthlyScores()
    {
        return $this->hasMany(StudentMonthlyScore::class);
    }

    public function sudokuLogs()
    {
        return $this->hasMany(SudokuLog::class);
    }

    public function physicalPerformances()
    {
        return $this->hasMany(PhysicalPerformance::class);
    }

    public function uploadedMaterials()
    {
        return $this->hasMany(Material::class, 'uploaded_by');
    }

    // Helper
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isSiswa(): bool
    {
        return $this->role === 'siswa';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
