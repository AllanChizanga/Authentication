<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'session_token' => ['required', 'string'],
            'fullname' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'country' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users,email'],
            'profile_photo' => ['nullable', 'image', 'max:2048'],
            'id_photo' => ['nullable', 'image', 'max:2048'],
            'work_location' => ['nullable', 'string', 'max:255'],
            'home_location' => ['nullable', 'string', 'max:255'],
            'gender' => ['required', 'in:male,female'],
            'payment_preference' => ['nullable', 'string', 'max:50'],
            'is_activated' => ['boolean'],
            'badge' => ['nullable', 'string', 'max:50'],
            'password' => ['nullable', 'string', 'min:4', 'confirmed'],
        ];
       
    }
}
