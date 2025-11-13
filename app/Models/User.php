<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'fullname',
        'national_id',
        'phone',
        'country',
        'city',
        'email',
        'profile_photo',
        'id_photo',
        'work_location',
        'home_location',
        'gender',
        'payment_preference',
        'is_activated',
        'badge',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

     /**
     * Find user by phone number
     */
    public static function findByPhoneNumber(string $phoneNumber): ?self
    {
        return static::where('phone', $phoneNumber)->first();
    }

    /**
     * Check if a phone number already exists in the users table.
     *
     * @param string $phoneNumber
     * @return bool
     */
    public static function phoneNumberExist($phoneNumber)
    {
        return self::where('phone_number', $phoneNumber)->exists();
    }

    /**
     * Check if user has verified phone
     */
    public function hasVerifiedPhone(): bool
    {
        return !is_null($this->phone_verified_at);
    }


    /**
     * OTP verification relationships
     */
    public function otpVerifications()
    {
        return $this->hasMany(Otp::class);
    }

    public function driver()
    {
        return $this->hasOne(Driver::class);
    }
}
