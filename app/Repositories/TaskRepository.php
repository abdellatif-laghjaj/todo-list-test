<?php

namespace App\Repositories;

use App\Models\Task;
use App\Models\User;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TaskRepository implements TaskRepositoryInterface
{
    /**
     * Get all tasks for a user.
     */
    public function getUserTasks(User $user, array $filters = []): LengthAwarePaginator
    {
        $query = $user->tasks()->orderBy('created_at', 'desc');

        // Apply filters
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (! empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%'.$filters['search'].'%')
                    ->orWhere('description', 'like', '%'.$filters['search'].'%');
            });
        }

        return $query->paginate(10);
    }

    /**
     * Find a task by ID for a specific user.
     */
    public function findByIdForUser(int $id, User $user): ?Task
    {
        return $user->tasks()->where('id', $id)->first();
    }

    /**
     * Create a new task.
     */
    public function create(array $data, User $user): Task
    {
        $data['user_id'] = $user->id;

        return Task::create($data);
    }

    /**
     * Update a task.
     */
    public function update(Task $task, array $data): Task
    {
        $task->update($data);

        return $task->fresh();
    }

    /**
     * Delete a task.
     */
    public function delete(Task $task): bool
    {
        return $task->delete();
    }

    /**
     * Get tasks statistics for a user.
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
