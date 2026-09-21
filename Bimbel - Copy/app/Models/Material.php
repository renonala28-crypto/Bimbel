<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'category',
        'title',
        'description',
        'type',
        'content',
        'allowed_students',
        'status',
        'file_path',
        'file_size',
        'uploaded_by',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
