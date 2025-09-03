<?php

namespace App\Repositories\Interfaces;

use App\Models\Task;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface TaskRepositoryInterface
{
    /**
     * Get all tasks for a user.
     *
     * @param User $user
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getUserTasks(User $user, array $filters = []): LengthAwarePaginator;

    /**
     * Find a task by ID for a specific user.
     *
     * @param int $id
     * @param User $user
     * @return Task|null
     */
    public function findByIdForUser(int $id, User $user): ?Task;

    /**
     * Create a new task.
     *
     * @param array $data
     * @param User $user
     * @return Task
     */
    public function create(array $data, User $user): Task;

    /**
     * Update a task.
     *
     * @param Task $task
     * @param array $data
     * @return Task
     */
    public function update(Task $task, array $data): Task;

    /**
     * Delete a task.
     *
     * @param Task $task
     * @return bool
     */
    public function delete(Task $task): bool;

    /**
     * Get tasks statistics for a user.
     *
     * @param User $user
     * @return array
     */
    public function getTasksStats(User $user): array;
}
