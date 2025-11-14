<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use App\DTOs\User\UpdateUserData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(private UserService $userService) {}
    
  
    
    /**
     * Get specific user details (admin only)
     */
    public function show(): JsonResponse
    {
        $user = auth()->user();
        $id = $user->id;
        $userData = $this->userService->getUser($id);
        
        return response()->json([
            'data' => $userData->toArray(),
        ]);
    }
    
    /**
     * Update authenticated user
     */
    public function updateAuthenticated(Request $request): JsonResponse
    {
        $data = UpdateUserData::from($request->all());
        
        $userData = $this->userService->updateAuthenticatedUser($data);
        
        return response()->json([
            'message' => 'User updated successfully',
            'data' => $userData->toArray(),
        ]);
    }
    
    /**
     * Update specific user (admin only)
     */
    public function update(Request $request, int $id): JsonResponse
    {
        // Add authorization check if needed
        // $this->authorize('update', User::findOrFail($id));
        
        $data = UpdateUserData::from($request->all());
        
        $userData = $this->userService->updateUser($id, $data);
        
        return response()->json([
            'message' => 'User updated successfully',
            'data' => $userData->toArray(),
        ]);
    }
    
    /**
     * Delete authenticated user
     */
    public function destroyAuthenticated(): JsonResponse
    {
        $this->userService->deleteAuthenticatedUser();
        
        return response()->json([
            'message' => 'User account deleted successfully',
        ]);
    }
    
    /**
     * Delete specific user (admin only)
     */
    public function destroy(int $id): JsonResponse
    {
        // Add authorization check if needed
        // $this->authorize('delete', User::findOrFail($id));
        
        $this->userService->deleteUser($id);
        
        return response()->json([
            'message' => 'User deleted successfully',
        ]);
    }
}