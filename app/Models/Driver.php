<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'license_url',
        'proof_of_residence_url',
        'police_clearance_letter_url',
        'number_of_completed_rides',
        'is_activated',
        'badge',
    ];

    protected $casts = [
        'is_activated' => 'boolean',
        'number_of_completed_rides' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isActive(): bool
    {
        return $this->is_activated && $this->badge !== 'red';
    }

    public function canAcceptRides(): bool
    {
        return $this->isActive() && 
               $this->license_url && 
               $this->badge !== 'red';
    }
}