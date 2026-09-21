<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $table = 'attendance';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'attendance_date',
        'status',
        'created_at',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
