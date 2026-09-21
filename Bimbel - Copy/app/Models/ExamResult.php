<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamResult extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'exam_id',
        'score',
        'correct_answers',
        'duration_spent',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'created_at'   => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exam()
    {
        return $this->belongsTo(ExamSession::class, 'exam_id');
    }
}
