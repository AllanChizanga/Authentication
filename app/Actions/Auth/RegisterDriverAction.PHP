<?php

namespace App\Actions\Drivers;

use App\DataTransferObjects\CreateDriverDataDTO;
use App\Services\DriverService;
use App\Models\Driver;

class RegisterDriverAction
{
    public function __construct(
        private DriverService $driverService
    ) {}

    public function execute(CreateDriverDataDTO $data): Driver
    {
        // Validate business rules before creating driver
        $this->validateDriverRegistration($data);

        return $this->driverService->createDriver($data);
    }

    protected function validateDriverRegistration(CreateDriverDataDTO $data): void
    {
        // Add any business logic validation here
        if (!$data->license_document) {
            throw new \InvalidArgumentException('License document is required for driver registration.');
        }

        // Check if user already has a driver profile
        if (Driver::where('user_id', $data->user_id)->exists()) {
            throw new \InvalidArgumentException('User already has a driver profile.');
        }

        // Validate file types if files are provided
        if ($data->license_document) {
            $allowedMimes = ['pdf', 'jpg', 'jpeg', 'png'];
            $extension = $data->license_document->getClientOriginalExtension();
            
            if (!in_array(strtolower($extension), $allowedMimes)) {
                throw new \InvalidArgumentException('License document must be PDF, JPG, or PNG.');
            }
        }
    }
}