<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentMonthlyScore extends Model
{
    protected $fillable = [
        'user_id',
        'student_name_custom',
        'period_year',
        'period_month',
        'recorded_date',
        'twk',
        'tiu',
        'tkp',
        'nilai_cat',
        'lari_meters',
        'push_up',
        'sit_up',
        'pull_up',
        'shuttle_seconds',
        'renang_seconds',
        'renang_distance',
        'total_samapta',
        'nilai_total',
    ];

    protected $casts = [
        'recorded_date' => 'date',
        'twk' => 'float',
        'tiu' => 'float',
        'tkp' => 'float',
        'nilai_cat' => 'float',
        'total_samapta' => 'float',
        'nilai_total' => 'float',
        'shuttle_seconds' => 'float',
        'renang_seconds' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
