<?php

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class DeleteUserAction
{
    public function execute(int $userId): bool
    {
        return DB::transaction(function () use ($userId) {
            $user = User::findOrFail($userId);
            
            return $user->delete();
        });
    }
    
    public function executeForAuthenticated(): bool
    {
        return DB::transaction(function () {
            $user = auth()->user();
            
            return $user->delete();
        });
    }
}