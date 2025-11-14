<?php

namespace App\Actions\User;

use App\Models\User;
use App\DTOs\User\UpdateUserData;
use App\DTOs\User\UserData;
use Illuminate\Support\Facades\DB;

class UpdateUserAction
{
    public function execute(int $userId, UpdateUserData $data): UserData
    {
        return DB::transaction(function () use ($userId, $data) {
            $user = User::findOrFail($userId);
            
            $user->update($data->toArray());
            
            return UserData::fromModel($user->fresh());
        });
    }
    
    public function executeForAuthenticated(UpdateUserData $data): UserData
    {
        return DB::transaction(function () use ($data) {
            $user = auth()->user();
            
            $user->update($data->toArray());
            
            return UserData::fromModel($user->fresh());
        });
    }
}