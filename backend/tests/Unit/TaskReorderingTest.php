<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Models\User;
use App\Repositories\TaskRepository;
use App\Services\TaskService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TaskReorderingTest extends TestCase
{
    use RefreshDatabase;

    private TaskService $taskService;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

    // Create a new TaskService for testing
    $this->taskService = new TaskService(new TaskRepository());
    // Create a user for testing
    $this->user = User::factory()->create();
    }

    #[Test]
    public function it_can_reorder_tasks()
    {
    // Create three tasks for the user, each with a starting order
        $task1 = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Task 1',
            'order' => 0,
        ]);
        $task2 = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Task 2',
            'order' => 1,
        ]);
        $task3 = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Task 3',
            'order' => 2,
        ]);

    // Change the order so that task3 comes first
        $newOrder = [
            $task3->id => 0,
            $task1->id => 1,
            $task2->id => 2,
        ];
        $this->taskService->reorderTasks($this->user, $newOrder);

    // Check that the tasks are now in the new order
        $this->assertDatabaseHas('tasks', [
            'id' => $task3->id,
            'order' => 0,
        ]);
        $this->assertDatabaseHas('tasks', [
            'id' => $task1->id,
            'order' => 1,
        ]);
        $this->assertDatabaseHas('tasks', [
            'id' => $task2->id,
            'order' => 2,
        ]);
    }

    #[Test]
    public function it_maintains_order_when_creating_new_task()
    {
    // Create two tasks for the user
        Task::factory()->create([
            'user_id' => $this->user->id,
            'order' => 0,
        ]);
        Task::factory()->create([
            'user_id' => $this->user->id,
            'order' => 1,
        ]);

    // Create a new task and expect it to have the next order value
        $newTask = $this->taskService->createTask($this->user, [
            'title' => 'New Task',
            'description' => 'Description',
        ]);

    // Check that the new task's order is 2
        $this->assertEquals(2, $newTask->order);
    }

    #[Test]
    public function it_returns_tasks_in_correct_order()
    {
    // Create three tasks out of order
        $task2 = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Second',
            'order' => 1,
        ]);
        $task1 = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'First',
            'order' => 0,
        ]);
        $task3 = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Third',
            'order' => 2,
        ]);

    // Get the user's tasks and expect them to be sorted by order
        $tasks = $this->taskService->getUserTasks($this->user);

    // Check that the tasks are in the right order
        $this->assertEquals('First', $tasks[0]->title);
        $this->assertEquals('Second', $tasks[1]->title);
        $this->assertEquals('Third', $tasks[2]->title);
    }
}
