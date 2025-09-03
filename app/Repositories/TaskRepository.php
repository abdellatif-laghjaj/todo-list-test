<?php

namespace App\Repositories;

use App\Models\Task;
use App\Models\User;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class TaskRepository implements TaskRepositoryInterface
{
    /**
     * Get all tasks for a user.
     *
     * @param User $user
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getUserTasks(User $user, array $filters = []): LengthAwarePaginator
    {
        $query = $user->tasks()->orderBy('created_at', 'desc');

        // Apply filters
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->paginate(10);
    }

    /**
     * Find a task by ID for a specific user.
     *
     * @param int $id
     * @param User $user
     * @return Task|null
     */
    public function findByIdForUser(int $id, User $user): ?Task
    {
        return $user->tasks()->where('id', $id)->first();
    }

    /**
     * Create a new task.
     *
     * @param array $data
     * @param User $user
     * @return Task
     */
    public function create(array $data, User $user): Task
    {
        $data['user_id'] = $user->id;
        
        return Task::create($data);
    }

    /**
     * Update a task.
     *
     * @param Task $task
     * @param array $data
     * @return Task
     */
    public function update(Task $task, array $data): Task
    {
        $task->update($data);
        
        return $task->fresh();
    }

    /**
     * Delete a task.
     *
     * @param Task $task
     * @return bool
     */
    public function delete(Task $task): bool
    {
        return $task->delete();
    }

    /**
     * Get tasks statistics for a user.
     *
     * @param User $user
     * @return array
     */
    public function getTasksStats(User $user): array
    {
        $tasks = $user->tasks();

        return [
            'total' => $tasks->count(),
            'pending' => $tasks->where('status', 'pending')->count(),
            'in_progress' => $tasks->where('status', 'in_progress')->count(),
            'completed' => $tasks->where('status', 'completed')->count(),
            'high_priority' => $tasks->where('priority', 'high')->count(),
        ];
    }
}
