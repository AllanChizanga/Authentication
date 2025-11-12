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
use App\Http\Requests\RegisterUserRequest;
use App\Services\TokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    protected $initiateRegistrationAction;
    protected $verifyRegistrationOtpAction;

    protected $completeRegistrationAction;

    public function __construct(InitiateRegistrationAction $initiateRegistrationAction, VerifyRegistrationOtpAction $verifyRegistrationOtpAction, CompleteRegistrationAction $completeRegistrationAction)
    {
        $this->initiateRegistrationAction = $initiateRegistrationAction;
        $this->verifyRegistrationOtpAction = $verifyRegistrationOtpAction;
        $this->completeRegistrationAction = $completeRegistrationAction;

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
    public function completeRegistration(Request $request)
    {
        $data = $request->validate([
    'session_token'      => 'required|string',
    'fullname'           => 'required|string|max:255',
    'phone'              => 'nullable|string|max:20',
    'country'            => 'required|string|max:100',
    'city'               => 'required|string|max:100',
    'email'              => 'nullable|string|email|max:255|unique:users,email',
    'profile_photo'      => 'nullable|image|max:2048',
    'id_photo'           => 'nullable|image|max:2048',
    'work_location'      => 'nullable|string|max:255',
    'home_location'      => 'nullable|string|max:255',
    'gender'             => 'required|in:male,female',
    'payment_preference' => 'nullable|string|max:50',
    'is_activated'       => 'boolean',
    'badge'              => 'nullable|string|max:50',
    'password'           => 'nullable|string|min:4',

        ]);
        return response()->json(['data'=>$data]);
        $dto = CompleteRegistrationDTO::from_request($data);

        $response = $this->completeRegistrationAction->execute($dto);

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