<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDriverRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|uuid|exists:users,id',
            'license_document' => 'required|file|mimes:pdf,jpg,png|max:10240',
            'proof_of_residence' => 'nullable|file|mimes:pdf,jpg,png|max:10240',
            'police_clearance_letter' => 'nullable|file|mimes:pdf,jpg,png|max:10240',
            'is_activated' => 'boolean',
            'badge' => 'in:red,green,yellow',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'User ID is required',
            'license_document.required' => 'License document is required',
            'license_document.mimes' => 'License must be PDF, JPG or PNG',
        ];
    }
}