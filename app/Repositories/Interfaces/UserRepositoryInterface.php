<?php

namespace App\Repositories\Interfaces;

use App\Models\User;

interface UserRepositoryInterface
{
    /**
     * Create a new user.
     */
    public function create(array $data): User;

    /**
     * Find a user by email.
     */
    public function findByEmail(string $email): ?User;

    /**
     * Find a user by ID.
     */
    public function findById(int $id): ?User;

    /**
     * Update user profile.
     */
    public function update(User $user, array $data): User;
}
