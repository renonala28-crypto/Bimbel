<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamSession extends Model
{
    protected $fillable = [
        'exam_name',
        'category',
        'description',
        'duration_minutes',
        'total_questions',
        'passing_score',
        'show_discussion',
        'allowed_students',
        'status',
    ];

    public function results()
    {
        return $this->hasMany(ExamResult::class, 'exam_id');
    }
}
