<?php

namespace App\Interfaces;

use App\Models\User;
use Illuminate\Contracts\Database\Query\Builder;

interface UserRepository
{
    public function getUserDataProcessingWith(?array $with = null): Builder;
    public function createUser(array $userData): User;
    public function updateUser(String $userUuid, array $userData): User;
    public function deleteUser(String $userUuid): void;
    public function syncLeader(): void;
    public function syncPlt(): void;
}
