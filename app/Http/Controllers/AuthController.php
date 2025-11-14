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
use App\Models\User;
use App\Services\TokenService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    protected $initiateRegistrationAction;
    protected $verifyRegistrationOtpAction;

    protected $completeRegistrationAction;

    protected $initiateLoginAction;

    protected $user_service;

    public function __construct(
        InitiateRegistrationAction $initiateRegistrationAction, VerifyRegistrationOtpAction $verifyRegistrationOtpAction, 
        CompleteRegistrationAction $completeRegistrationAction, InitiateLoginAction $initiateLoginAction,
        UserService $userService,
        )
    {
        $this->initiateRegistrationAction = $initiateRegistrationAction;
        $this->verifyRegistrationOtpAction = $verifyRegistrationOtpAction;
        $this->completeRegistrationAction = $completeRegistrationAction;
        $this->initiateLoginAction = $initiateLoginAction;
        $this->user_service = $userService;

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
public function completeRegistration(RegisterUserRequest $request)
{

 
    $data = $request->validated();
    
    // Use from_array instead of from_request
    $dto = CompleteRegistrationDTO::from_array($data);
  
    $response = $this->completeRegistrationAction->execute($dto);
    
    return response()->json([
        'message' => 'Registration completed successfully',
        ...$response
    ], 201);
}

    /**
     * Initiate login - Step 1: Send OTP to phone
     */
    public function initiateLogin(Request $request): JsonResponse
     {
        $request->validate([
            'phone_number' => 'required|string|max:20',
        ]);


        $dto = new InitiateAuthDTO(
            phone_number: $request->input('phone_number'),
            purpose: 'login'
        );

        $response = $this->initiateLoginAction->execute($dto);
        if (empty($response['otp_code'])) {
    return response()->json([
        'success' => false,
        'message' => 'Authentication failed',
        'errors' => $response
    ], 401);
}
        return response()->json(['data',$response],200);
    }

    /**
     * Verify login OTP - Step 2: Verify OTP code
     */
    public function verifyLoginOtp(Request $request, VerifyLoginOtpAction $verifyLoginOtpAction): JsonResponse {
        $request->validate([
            'phone_number' => 'required|string|max:20',
            'otp_code' => 'required|string|size:6',
            'session_token' => 'required|string',
            'purpose' => 'required|string|in:registration,login',
        ]);

        $dto = VerifyOtpDTO::from_request($request);

        $response = $verifyLoginOtpAction->execute($dto);

        return response()->json($response);
    }

    /**
     * Complete login - Step 3: Authenticate user
     */
    public function completeLogin(Request $request, CompleteLoginAction $completeLoginAction): JsonResponse 
    {
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
    public function user(): JsonResponse
    {

        $user = $this->user_service->get_user();

        return response()->json([
            'data' => $user
        ]);
    }

    public function checkAuth(): JsonResponse
    {
        $user = $this->user_service->check_token();
        return response()->json(['data' => $user]);
    }
}