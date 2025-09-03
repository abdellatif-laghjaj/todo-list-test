<?php

namespace App\Services;

use App\Events\TaskCreated;
use App\Events\TaskDeleted;
use App\Events\TaskUpdated;
use App\Models\Task;
use App\Models\User;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TaskService
{
    protected TaskRepositoryInterface $taskRepository;

    public function __construct(TaskRepositoryInterface $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    /**
     * Get paginated tasks for the authenticated user.
     */
    public function getUserTasks(User $user, array $filters = []): LengthAwarePaginator
    {
        return $this->taskRepository->getUserTasks($user, $filters);
    }

    /**
     * Get a specific task for the authenticated user.
     *
     * @throws ModelNotFoundException
     */
    public function getTask(int $id, User $user): Task
    {
        $task = $this->taskRepository->findByIdForUser($id, $user);

        if (! $task) {
            throw new ModelNotFoundException('Task not found or you do not have permission to access it.');
        }

        return $task;
    }

    /**
     * Create a new task.
     */
    public function createTask(array $data, User $user): Task
    {
        $task = $this->taskRepository->create($data, $user);

        // Broadcast task created event
        broadcast(new TaskCreated($task, $user));

        return $task;
    }

    /**
     * Update an existing task.
     *
     * @throws ModelNotFoundException
     */
    public function updateTask(int $id, array $data, User $user): Task
    {
        $task = $this->getTask($id, $user);
        $updatedTask = $this->taskRepository->update($task, $data);

        // Broadcast task updated event
        broadcast(new TaskUpdated($updatedTask, $user));

        return $updatedTask;
    }

    /**
     * Delete a task.
     *
     * @throws ModelNotFoundException
     */
    public function deleteTask(int $id, User $user): bool
    {
        $task = $this->getTask($id, $user);
        $result = $this->taskRepository->delete($task);

        if ($result) {
            // Broadcast task deleted event
            broadcast(new TaskDeleted($task, $user));
        }

        return $result;
    }

    /**
     * Get tasks statistics for a user.
     */
    public function getTasksStats(User $user): array
    {
        return $this->taskRepository->getTasksStats($user);
    }
}
