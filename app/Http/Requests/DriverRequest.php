<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DriverRequest extends FormRequest
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
        'license_url' => 'required',
        'proof_of_residence_url' => 'nullable',
        'police_clearance_letter_url' => 'nullable',
        'number_of_completed_rides' => 'nullable',
        'is_activated' => 'nullable',
        'badge' => 'nullable',
        ];
    }
}
