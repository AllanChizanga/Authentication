<?php

namespace App\Actions\Auth;

use App\DTOs\RegisterUserDTO;
use App\Services\AuthService;

/**
 * Action class for user registration
 * Orchestrates the registration process using services
 * Single responsibility: handle user registration workflow
 */
class RegisterUserAction
{
    public function __construct(
        private AuthService $auth_service
    ) {}

    /**
     * Execute the user registration process
     * 1. Register user via AuthService
     * 2. Create authentication response
     * Returns array with user data and token
     */
    public function execute(RegisterUserDTO $dto): array
    {
        // Convert DTO to array and register user
        $user = $this->auth_service->register_user($dto->to_array());

        // Create and return authentication response
        return $this->auth_service->create_auth_response($user);
    }
}