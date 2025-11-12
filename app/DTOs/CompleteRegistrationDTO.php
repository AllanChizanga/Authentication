<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class CompleteRegistrationDTO
{
    public function __construct(
        public string $fullname,
        public string $email = '',
        public string $national_id,
        public string $phone,
        public string $country,
        public string $city,
        public ?string $profile_photo = null,
        public ?string $id_photo = null,
        public ?string $work_location = null,
        public ?string $home_location = null,
        public ?string $gender = null,
        public string $payment_preference = 'cash',
        public bool $is_activated = false,
        public string $badge = 'red',
        public string $password = '',
        public ?string $session_token = null,
    ) {}

    /**
     * Create DTO from HTTP Request
     */
    public static function from_request(Request $request): self
    {
        return new self(
            fullname: $request->input('fullname'),
            email: $request->input('email'),
            national_id: $request->input('national_id', ''),
            phone: $request->input('phone', ''),
            country: $request->input('country', ''),
            city: $request->input('city', ''),
            profile_photo: $request->input('profile_photo'),
            id_photo: $request->input('id_photo'),
            work_location: $request->input('work_location'),
            home_location: $request->input('home_location'),
            gender: $request->input('gender'),
            payment_preference: $request->input('payment_preference', 'cash'),
            is_activated: $request->boolean('is_activated', false),
            badge: $request->input('badge', 'red'),
            password:$request->input('password'),
            session_token: $request->input('session_token'),
        );
    }

    /**
     * Create DTO from array
     */
    public static function from_array(array $data): self
    {
        return new self(
            fullname: $data['fullname'] ?? '',
            email: $data['email'] ?? '',
            national_id: $data['national_id'] ?? '',
            phone: $data['phone'] ?? '',
            country: $data['country'] ?? '',
            city: $data['city'] ?? '',
            profile_photo: $data['profile_photo'] ?? null,
            id_photo: $data['id_photo'] ?? null,
            work_location: $data['work_location'] ?? null,
            home_location: $data['home_location'] ?? null,
            gender: $data['gender'] ?? null,
            payment_preference: $data['payment_preference'] ?? 'cash',
            is_activated: $data['is_activated'] ?? false,
            badge: $data['badge'] ?? 'red',
            password:$data['password']??'',
            session_token: $data['session_token'] ?? null,
        );
    }

    /**
     * Convert DTO to array for database operations
     */
    public function to_array(): array
    {
        return [
            'name' => $this->fullname,
            'fullname' => $this->fullname,
            'email' => $this->email,
            'national_id' => $this->national_id,
            'phone' => $this->phone,
            'country' => $this->country,
            'city' => $this->city,
            'profile_photo' => $this->profile_photo,
            'id_photo' => $this->id_photo,
            'work_location' => $this->work_location,
            'home_location' => $this->home_location,
            'gender' => $this->gender,
            'payment_preference' => $this->payment_preference,
            'is_activated' => $this->is_activated,
            'badge' => $this->badge,
            'password' => $this->password,
            'session_token' => $this->session_token,
        ];
    }

    /**
     * Convert DTO to array for database operations (alias)
     */
    public function toDatabaseArray(): array
    {
        return $this->to_array();
    }

    /**
     * Get only the filled (non-null) values
     */
    public function get_filled_values(): array
    {
        return array_filter($this->to_array(), function ($value) {
            return $value !== null && $value !== '';
        });
    }

    /**
     * Get only the non-empty values
     */
    public function getNonEmptyValues(): array
    {
        return array_filter($this->to_array(), function ($value) {
            if (is_string($value)) {
                $value = trim($value);
            }
            return $value !== null && $value !== '' && $value !== [];
        });
    }

    /**
     * Validate required fields for registration completion
     */
    public function validate_required_fields(): void
    {
        $required = ['fullname', 'phone', 'country', 'city', 'national_id'];
        
        foreach ($required as $field) {
            if (empty($this->{$field})) {
                throw new \InvalidArgumentException("The {$field} field is required.");
            }
        }
    }

    /**
     * Validate required fields with custom error messages
     */
    public function validateWithCustomMessages(array $messages = []): void
    {
        $defaultMessages = [
            'fullname' => 'Full name is required',
            'phone' => 'Phone number is required',
            'country' => 'Country is required',
            'city' => 'City is required',
            'national_id' => 'National ID is required',
        ];

        $messages = array_merge($defaultMessages, $messages);
        
        foreach ($messages as $field => $message) {
            if (empty($this->{$field})) {
                throw new \InvalidArgumentException($message);
            }
        }
    }

    /**
     * Check if all required documents are provided
     */
    public function has_required_documents(): bool
    {
        return !empty($this->profile_photo) && !empty($this->id_photo);
    }

    /**
     * Get document status
     */
    public function get_document_status(): array
    {
        return [
            'profile_photo' => !empty($this->profile_photo),
            'id_photo' => !empty($this->id_photo),
            'all_documents_provided' => $this->has_required_documents(),
        ];
    }

    /**
     * Check if user can be activated (has all required info)
     */
    public function can_be_activated(): bool
    {
        return !empty($this->fullname) &&
               !empty($this->national_id) &&
               !empty($this->phone) &&
               !empty($this->country) &&
               !empty($this->city) &&
               $this->has_required_documents();
    }

    /**
     * Get activation readiness status
     */
    public function get_activation_status(): array
    {
        return [
            'has_personal_info' => !empty($this->fullname) && !empty($this->national_id) && !empty($this->phone),
            'has_location_info' => !empty($this->country) && !empty($this->city),
            'has_documents' => $this->has_required_documents(),
            'can_be_activated' => $this->can_be_activated(),
            'missing_requirements' => $this->get_missing_requirements(),
        ];
    }

    /**
     * Get missing requirements for activation
     */
    public function get_missing_requirements(): array
    {
        $missing = [];

        if (empty($this->fullname)) $missing[] = 'fullname';
        if (empty($this->national_id)) $missing[] = 'national_id';
        if (empty($this->phone)) $missing[] = 'phone';
        if (empty($this->country)) $missing[] = 'country';
        if (empty($this->city)) $missing[] = 'city';
        if (empty($this->profile_photo)) $missing[] = 'profile_photo';
        if (empty($this->id_photo)) $missing[] = 'id_photo';

        return $missing;
    }

    /**
     * Update specific fields
     */
    public function update(array $data): self
    {
        $updatedData = array_merge($this->to_array(), $data);
        return self::from_array($updatedData);
    }

    /**
     * Check if the DTO has session token
     */
    public function has_session_token(): bool
    {
        return !empty($this->session_token);
    }

    /**
     * Get only personal information (without documents and system fields)
     */
    public function get_personal_info(): array
    {
        return [
            'fullname' => $this->fullname,
            'national_id' => $this->national_id,
            'phone' => $this->phone,
            'country' => $this->country,
            'city' => $this->city,
            'gender' => $this->gender,
            'work_location' => $this->work_location,
            'home_location' => $this->home_location,
        ];
    }

    /**
     * Get only document information
     */
    public function get_document_info(): array
    {
        return [
            'profile_photo' => $this->profile_photo,
            'id_photo' => $this->id_photo,
        ];
    }

    /**
     * Get only system/preference information
     */
    public function get_system_info(): array
    {
        return [
            'payment_preference' => $this->payment_preference,
            'is_activated' => $this->is_activated,
            'badge' => $this->badge,
            'session_token' => $this->session_token,
        ];
    }

    /**
     * Create a new instance with only the provided fields changed
     */
    public function with(array $changes): self
    {
        $currentData = $this->to_array();
        $updatedData = array_merge($currentData, $changes);
        
        return self::from_array($updatedData);
    }

    /**
     * Convert to JSON string
     */
    public function to_json(): string
    {
        return json_encode($this->to_array(), JSON_PRETTY_PRINT);
    }

    /**
     * Create from JSON string
     */
    public static function from_json(string $json): self
    {
        $data = json_decode($json, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid JSON provided');
        }
        
        return self::from_array($data);
    }

    /**
     * Check if two DTOs are equal
     */
    public function equals(self $other): bool
    {
        return $this->to_array() === $other->to_array();
    }

    /**
     * Get array of changed fields compared to another DTO
     */
    public function get_changed_fields(self $other): array
    {
        $changes = [];
        $current = $this->to_array();
        $otherArray = $other->to_array();

        foreach ($current as $key => $value) {
            if ($value !== $otherArray[$key]) {
                $changes[$key] = [
                    'from' => $otherArray[$key],
                    'to' => $value
                ];
            }
        }

        return $changes;
    }
}