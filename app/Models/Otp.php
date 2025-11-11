<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    use HasFactory, HasUuids;
    protected $table = 'otps';
    protected $fillable = [
        'user_id',
        'otp_code',
        'phone_number',
        'session_token',
        'expires_at',
        'verified_at',
        'attempts',
        'purpose',
    ];

    /*
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if OTP is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if OTP is verified
     */
    public function isVerified(): bool
    {
        return !is_null($this->verified_at);
    }

    /**
     * Check if OTP is valid (not expired and not verified)
     */
    public function isValid(): bool
    {
        return !$this->isExpired() && !$this->isVerified();
    }

    /**
     * Mark OTP as verified
     */
    public function markAsVerified(): bool
    {
        return $this->update(['verified_at' => now()]);
    }

    /**
     * Increment attempt count
     */
    public function incrementAttempts(): bool
    {
        return $this->increment('attempts');
    }

}
