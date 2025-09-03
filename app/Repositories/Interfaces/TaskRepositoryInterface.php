<?php

namespace App\Repositories\Interfaces;

use App\Models\Task;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TaskRepositoryInterface
{
    /**
     * Get all tasks for a user.
     */
    public function getUserTasks(User $user, array $filters = []): LengthAwarePaginator;

    /**
     * Find a task by ID for a specific user.
     */
    public function findByIdForUser(int $id, User $user): ?Task;

    /**
     * Create a new task.
     */
    public function create(array $data, User $user): Task;

    /**
     * Update a task.
     */
    public function update(Task $task, array $data): Task;

    /**
     * Delete a task.
     */
    public function delete(Task $task): bool;

    /**
     * Get tasks statistics for a user.
     */
    public function getTasksStats(User $user): array;
}
