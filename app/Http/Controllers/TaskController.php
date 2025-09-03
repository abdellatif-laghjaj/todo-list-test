<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Services\TaskService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class TaskController
 *
 * Handles CRUD operations for tasks with proper authorization
 * ensuring users can only access their own tasks.
 */
class TaskController extends Controller
{
    protected TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
        $this->middleware('auth:api');
    }

    /**
     * Display a paginated listing of tasks for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = [
                'status' => $request->get('status'),
                'priority' => $request->get('priority'),
                'search' => $request->get('search'),
            ];

            $tasks = $this->taskService->getUserTasks(auth()->user(), $filters);

            return response()->json([
                'status' => 'success',
                'data' => $tasks,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve tasks',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created task in storage.
     */
    public function store(TaskRequest $request): JsonResponse
    {
        try {
            $task = $this->taskService->createTask(
                $request->validated(),
                auth()->user()
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Task created successfully',
                'data' => $task,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create task',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified task.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $task = $this->taskService->getTask($id, auth()->user());

            return response()->json([
                'status' => 'success',
                'data' => $task,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Task not found or access denied',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve task',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified task in storage.
     */
    public function update(TaskRequest $request, int $id): JsonResponse
    {
        try {
            $task = $this->taskService->updateTask(
                $id,
                $request->validated(),
                auth()->user()
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Task updated successfully',
                'data' => $task,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Task not found or access denied',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update task',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified task from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->taskService->deleteTask($id, auth()->user());

            return response()->json([
                'status' => 'success',
                'message' => 'Task deleted successfully',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Task not found or access denied',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete task',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get tasks statistics for the authenticated user.
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = $this->taskService->getTasksStats(auth()->user());

            return response()->json([
                'status' => 'success',
                'data' => $stats,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve task statistics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
