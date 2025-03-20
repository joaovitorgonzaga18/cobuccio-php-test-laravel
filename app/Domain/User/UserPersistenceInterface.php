<?php

namespace App\Domain\User;

interface UserPersistenceInterface
{
    public function create(User $user): void;
    public function isEmailAlreadyCreated(User $user): bool;
    public function findAll(User $user): array;
    public function getUser(User $user): User;
    public function deleteUser(User $user): void;
    public function editName(User $user): void;
    public function editEmail(User $user): void;
    public function updateCurrency(User $user): void;
}
