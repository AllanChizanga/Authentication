<?php

namespace App\Http\Controllers;

use App\DTOs\LoginUserDTO;
use Illuminate\Http\Request;
use App\DTOs\RegisterUserDTO;
use App\Services\AuthService;
use App\Services\TokenService;
use Illuminate\Http\JsonResponse;
use App\Actions\Auth\LoginUserAction;
use App\Http\Requests\LoginUserRequest;
use App\Actions\Auth\RegisterUserAction;
use Illuminate\Validation\Rules\Password;
use App\Http\Requests\RegisterUserRequest;

class AuthController extends Controller
{
    /**
     * Register a new user
     * Uses RegisterUserAction to handle the registration process
     */
    public function register(RegisterUserRequest $request, RegisterUserAction $register_user_action): JsonResponse
     {
        // Validate incoming request data
        $data = $request->validated();

        // Create DTO from validated request
        $registerDTO = RegisterUserDTO::from_request($data);
        
        // Execute registration action
        $response = $register_user_action->execute($registerDTO);

        // Return JSON response with 201 status
        return response()->json([
            'message' => 'User registered successfully'], 200);
    }

    /**
     * Login user
     * Uses LoginUserAction to handle authentication
     */
    public function login(LoginUserRequest $request,LoginUserAction $login_user_action): JsonResponse 
    {
        // Validate login credentials
        $data = $request->validated();

        // Create DTO from request
        $loginDTO = LoginUserDTO::from_request($data);
        
        // Execute login action
        $response = $login_user_action->execute($loginDTO);

        return response()->json([
            'message' => 'Login successful',
            ...$response
        ]);
    }

    /**
     * Logout user (revoke current token)
     * Uses TokenService to handle token revocation
     */
    public function logout(Request $request, TokenService $tokenService): JsonResponse
    {
        // Delete current access token
        $tokenService->delete_current_token($request->user());

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * Get authenticated user data
     */
    public function user(Request $request): JsonResponse
    {
        return response()->json([
            'user' => [
                'id' => $request->user()->id,
                'name' => $request->user()->name,
                'email' => $request->user()->email,
                'email_verified_at' => $request->user()->email_verified_at,
            ]
        ]);
    }

    /**
     * Refresh authentication token
     * Revokes current token and issues new one
     */
    public function refresh(Request $request, TokenService $tokenService): JsonResponse
    {
        $user = $request->user();
        
        // Delete current token
        $tokenService->delete_current_token($user);
        
        // Create new token using AuthService
        $authService = app(AuthService::class);
        $response = $authService->create_auth_response($user);

        return response()->json([
            'message' => 'Token refreshed successfully',
            ...$response
        ]);
    }

    /**
     * Logout from all devices (revoke all tokens)
     */
    public function logoutAllDevices(Request $request, TokenService $tokenService): JsonResponse
    {
        $tokenService->delete_all_tokens($request->user());

        return response()->json([
            'message' => 'Successfully logged out from all devices'
        ]);
    }
}