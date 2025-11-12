<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'session_token' => 'nullable|string',
            'fullname' => 'nullable|string|max:255',
            'national_id' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'profile_photo' => 'nullable|string',
            'id_photo' => 'nullable|string',
            'work_location' => 'nullable|string',
            'home_location' => 'nullable|string',
            'gender' => 'nullable|string|in:male,female,other',
            'payment_preference' => 'nullable|string|in:cash,card,mobile',
            'is_activated' => 'nullable|boolean',
            'badge' => 'nullable|string|in:red,green,yellow',
        ];
    }
}