<?php

namespace App\Services;

use App\Actions\User\GetUserAction;
use App\Actions\User\UpdateUserAction;
use App\Actions\User\DeleteUserAction;
use App\DTOs\User\UserData;
use App\DTOs\User\UpdateUserData;

class UserService
{
    public function __construct(
        private GetUserAction $getUserAction,
        private UpdateUserAction $updateUserAction,
        private DeleteUserAction $deleteUserAction,
    ) {}
    
    public function getUser(int $userId): UserData
    {
        return $this->getUserAction->execute($userId);
    }
    
    public function getAuthenticatedUser(): UserData
    {
        return $this->getUserAction->executeForAuthenticated();
    }
    
    public function updateUser(int $userId, UpdateUserData $data): UserData
    {
        return $this->updateUserAction->execute($userId, $data);
    }
    
    public function updateAuthenticatedUser(UpdateUserData $data): UserData
    {
        return $this->updateUserAction->executeForAuthenticated($data);
    }
    
    public function deleteUser(int $userId): bool
    {
        return $this->deleteUserAction->execute($userId);
    }
    
    public function deleteAuthenticatedUser(): bool
    {
        return $this->deleteUserAction->executeForAuthenticated();
    }

    
}