<?php

declare(strict_types=1);

namespace App\Interfaces;

interface UsersInterfaces
{
    public function readAllUsers(): void;
    public function readOneUser(int|string $id): void;
    public function createUser(): void;
    public function updateUser(int|string $id): void;
    public function deleteUser(int|string $id): void;
}
