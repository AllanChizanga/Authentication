<?php

namespace App\Http\Controllers;

use App\Actions\Auth\{
    InitiateRegistrationAction,
    VerifyRegistrationOtpAction,
    CompleteRegistrationAction,
    InitiateLoginAction,
    VerifyLoginOtpAction,
    CompleteLoginAction
};
use App\DTOs\{
    InitiateAuthDTO,
    VerifyOtpDTO,
    CompleteRegistrationDTO,
    CompleteLoginDTO
};
use App\Services\TokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    protected $initiateRegistrationAction;
    protected $verifyRegistrationOtpAction;

    public function __construct(InitiateRegistrationAction $initiateRegistrationAction, VerifyRegistrationOtpAction $verifyRegistrationOtpAction)
    {
        $this->initiateRegistrationAction = $initiateRegistrationAction;
        $this->verifyRegistrationOtpAction = $verifyRegistrationOtpAction;

    }
    /**
     * Initiate registration - Step 1: Send OTP to phone
     */
    public function initiateRegistration(Request $request): JsonResponse 
    {
        
       

        $request->validate([
            'phone_number' => 'required|string|max:20',
        ]);

        $dto = new InitiateAuthDTO(
            phone_number: $request->input('phone_number'),
            purpose: 'registration'
        );
        
        $response = $this->initiateRegistrationAction->execute($dto);

    return response()->json(['data'=>$response]);
    }

    /**
     * Verify registration OTP - Step 2: Verify OTP code
     */
    public function verifyRegistrationOtp(Request $request): JsonResponse {
        $request->validate([
            'phone_number' => 'required|string|max:20',
            'otp_code' => 'required|string|size:6',
            'session_token' => 'required|string',
        ]);

        
        $dto = VerifyOtpDTO::from_request($request);
        
        $response = $this->verifyRegistrationOtpAction->execute($dto);
        
        return response()->json(['message' => $response]);
       
    }

    /**
     * Complete registration - Step 3: Create user account
     */
    public function completeRegistration(Request $request, CompleteRegistrationAction $completeRegistrationAction ): JsonResponse 
    {
        $request->validate([
            'session_token' => 'required|string',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $dto = CompleteRegistrationDTO::from_request($request);

        $response = $completeRegistrationAction->execute($dto);

        return response()->json([
            'message' => 'Registration completed successfully',
            ...$response
        ], 201);
    }

    /**
     * Initiate login - Step 1: Send OTP to phone
     */
    public function initiateLogin(
        Request $request,
        InitiateLoginAction $initiateLoginAction
    ): JsonResponse {
        $request->validate([
            'phone_number' => 'required|string|max:20',
        ]);

        $dto = new InitiateAuthDTO(
            phone_number: $request->input('phone_number'),
            purpose: 'login'
        );

        $response = $initiateLoginAction->execute($dto);

        return response()->json($response);
    }

    /**
     * Verify login OTP - Step 2: Verify OTP code
     */
    public function verifyLoginOtp(
        Request $request,
        VerifyLoginOtpAction $verifyLoginOtpAction
    ): JsonResponse {
        $request->validate([
            'phone_number' => 'required|string|max:20',
            'otp_code' => 'required|string|size:6',
            'session_token' => 'required|string',
        ]);

        $dto = VerifyOtpDTO::from_request($request);

        $response = $verifyLoginOtpAction->execute($dto);

        return response()->json($response);
    }

    /**
     * Complete login - Step 3: Authenticate user
     */
    public function completeLogin(
        Request $request,
        CompleteLoginAction $completeLoginAction
    ): JsonResponse {
        $request->validate([
            'session_token' => 'required|string',
        ]);

        $dto = CompleteLoginDTO::from_request($request);

        $response = $completeLoginAction->execute($dto);

        return response()->json([
            'message' => 'Login successful',
            ...$response
        ]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request, TokenService $tokenService): JsonResponse
    {
        $tokenService->delete_current_token($request->user());

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * Get authenticated user
     */
    public function user(Request $request): JsonResponse
    {
        return response()->json([
            'user' => [
                'id' => $request->user()->id,
                'name' => $request->user()->name,
                'email' => $request->user()->email,
                'phone_number' => $request->user()->phone_number,
                'phone_verified_at' => $request->user()->phone_verified_at,
                'email_verified_at' => $request->user()->email_verified_at,
            ]
        ]);
    }
}