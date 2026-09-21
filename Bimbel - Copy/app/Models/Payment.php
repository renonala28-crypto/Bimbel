<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'amount',
        'proof_file',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'created_at'  => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isVerified(): bool
    {
        return !is_null($this->verified_at);
    }
}
