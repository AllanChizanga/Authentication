<?php

namespace App\Actions\User;

use App\Models\User;
use App\DTOs\User\UserData;

class GetUserAction
{
    public function execute(int $userId): UserData
    {
        $user = User::findOrFail($userId);
        
        return UserData::fromModel($user);
    }
    
    public function executeForAuthenticated(): UserData
    {
        $user = auth()->user();
        
        return UserData::fromModel($user);
    }
}