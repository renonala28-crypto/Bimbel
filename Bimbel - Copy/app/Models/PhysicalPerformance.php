<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhysicalPerformance extends Model
{
    protected $table = 'physical_performance';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'lari_distance_km',
        'lari_time_minutes',
        'renang_distance_m',
        'renang_time_minutes',
        'recorded_date',
        'created_at',
    ];

    protected $casts = [
        'recorded_date' => 'date',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
