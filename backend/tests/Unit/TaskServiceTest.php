<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Models\User;
use App\Repositories\TaskRepository;
use App\Services\TaskService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TaskServiceTest extends TestCase
{
    use RefreshDatabase;

    private TaskService $taskService;
    private User $user;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // I make a new TaskService for testing
        $this->taskService = new TaskService(new TaskRepository());

        // I create a regular user and an admin for testing
        $this->user = User::factory()->create();
        $this->admin = User::factory()->create(['is_admin' => true]);
    }

    #[Test]
    public function it_can_create_a_task_for_user()
    {
        // I set up the data for a new task
        $taskData = [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'priority' => 'high',
            'status' => 'pending',
        ];

        // I use the service to create a task
        $task = $this->taskService->createTask($this->user, $taskData);

        // I check that the task was created correctly
        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals('Test Task', $task->title);
        $this->assertEquals($this->user->id, $task->user_id);
        $this->assertEquals(0, $task->order); // The first task should have order 0
        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'user_id' => $this->user->id,
        ]);
    }

    #[Test]
    public function it_can_update_a_task()
    {
        // I make a task for the user
        $task = Task::factory()->create(['user_id' => $this->user->id]);
        $updateData = [
            'title' => 'Updated Title',
            'status' => 'completed',
        ];

        // I use the service to update the task
        $updatedTask = $this->taskService->updateTask($task, $this->user, $updateData);

        // I check that the task was updated
        $this->assertEquals('Updated Title', $updatedTask->title);
        $this->assertEquals('completed', $updatedTask->status);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Title',
            'status' => 'completed',
        ]);
    }

    #[Test]
    public function it_prevents_non_owner_from_updating_task()
    {
        // I make a task for the user and another user
        $task = Task::factory()->create(['user_id' => $this->user->id]);
        $otherUser = User::factory()->create();

        // I expect an exception if someone else tries to update the task
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unauthorized to update this task');

        $this->taskService->updateTask($task, $otherUser, ['title' => 'Hacked']);
    }

    #[Test]
    public function it_allows_admin_to_update_any_task()
    {
        // I make a task for the user
        $task = Task::factory()->create(['user_id' => $this->user->id]);
        $updateData = ['title' => 'Admin Updated'];

        // I use the service as admin to update the task
        $updatedTask = $this->taskService->updateTask($task, $this->admin, $updateData);

        // I check that the update worked
        $this->assertEquals('Admin Updated', $updatedTask->title);
    }

    #[Test]
    public function it_can_delete_a_task()
    {
        // I make a task for the user
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        // I use the service to delete the task
        $result = $this->taskService->deleteTask($task, $this->user);

        // I check that the task was deleted
        $this->assertTrue($result);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    #[Test]
    public function it_prevents_non_owner_from_deleting_task()
    {
        // I make a task for the user and another user
        $task = Task::factory()->create(['user_id' => $this->user->id]);
        $otherUser = User::factory()->create();

        // I expect an exception if someone else tries to delete the task
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unauthorized to delete this task');

        $this->taskService->deleteTask($task, $otherUser);
    }

    #[Test]
    public function it_filters_tasks_by_status()
    {
        // I make 3 pending and 2 completed tasks for the user
        Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'status' => 'pending',
        ]);
        Task::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'status' => 'completed',
        ]);

        // I use the service to get only pending or only completed tasks
        $pendingTasks = $this->taskService->getUserTasks($this->user, ['status' => 'pending']);
        $completedTasks = $this->taskService->getUserTasks($this->user, ['status' => 'completed']);

        // I check that the counts are correct
        $this->assertCount(3, $pendingTasks);
        $this->assertCount(2, $completedTasks);
    }

    #[Test]
    public function it_calculates_user_statistics_correctly()
    {
        // I make 3 high-priority pending and 2 low-priority completed tasks for the user
        Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'status' => 'pending',
            'priority' => 'high',
        ]);
        Task::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'status' => 'completed',
            'priority' => 'low',
        ]);

        // I use the service to get the user's stats
        $stats = $this->taskService->getUserStatistics($this->user->id);

        // I check that the stats are correct
        $this->assertEquals(5, $stats['total']);
        $this->assertEquals(2, $stats['completed']);
        $this->assertEquals(3, $stats['pending']);
        $this->assertEquals(3, $stats['by_priority']['high']);
        $this->assertEquals(2, $stats['by_priority']['low']);
    }
}
