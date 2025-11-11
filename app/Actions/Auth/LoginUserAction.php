<?php

namespace App\Actions\Auth;

use App\DTOs\LoginUserDTO;
use App\Services\AuthService;

/**
 * Action class for user login
 * Orchestrates the login authentication process
 * Single responsibility: handle user login workflow
 */
class LoginUserAction
{
    public function __construct(
        private AuthService $auth_service
    ) {}

    /**
     * Execute the user login process
     * 1. Authenticate user credentials via AuthService
     * 2. Create authentication response
     * Returns array with user data and token
     */
    public function execute(LoginUserDTO $dto): array
    {
        // Authenticate user with credentials from DTO
        $user = $this->auth_service->authenticate_user($dto->to_array());

        // Create and return authentication response
        return $this->auth_service->create_auth_response($user);
    }
}