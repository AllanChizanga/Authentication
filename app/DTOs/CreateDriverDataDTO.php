<?php

namespace App\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;
use Illuminate\Http\UploadedFile;

class CreateDriverDataDTO extends DataTransferObject
{
    public string $user_id;
    public ?UploadedFile $license_document;
    public ?UploadedFile $proof_of_residence;
    public ?UploadedFile $police_clearance_letter;
    public bool $is_activated = false;
    public string $badge = 'red';
    
    public static function fromRequest(array $data): self
    {
        return new self([
            'user_id' => $data['user_id'],
            'license_document' => $data['license_document'] ?? null,
            'proof_of_residence' => $data['proof_of_residence'] ?? null,
            'police_clearance_letter' => $data['police_clearance_letter'] ?? null,
            'is_activated' => $data['is_activated'] ?? false,
            'badge' => $data['badge'] ?? 'red',
        ]);
    }
}