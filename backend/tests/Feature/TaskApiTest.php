<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    #[Test]
    public function it_requires_authentication_to_access_tasks()
    {
        // Act: Try to get tasks without logging in
        $response = $this->getJson('/api/tasks');

        // Assert: Should get a 401 Unauthorized response
        $response->assertStatus(401);
    }

    #[Test]
    public function it_can_list_user_tasks()
    {
        // Arrange: Log in and create 3 tasks for the user
        Sanctum::actingAs($this->user);
        Task::factory()->count(3)->create(['user_id' => $this->user->id]);

        // Act: Get the list of tasks
        $response = $this->getJson('/api/tasks');

        // Assert: Should get 3 tasks in the response
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'description', 'status', 'priority', 'order'],
                ],
            ]);
    }

    #[Test]
    public function it_can_create_a_task()
    {
        // Arrange: Log in and set up task data
        Sanctum::actingAs($this->user);
        $taskData = [
            'title' => 'New Task',
            'description' => 'Task Description',
            'priority' => 'high',
        ];

        // Act: Send a request to create a new task
        $response = $this->postJson('/api/tasks', $taskData);

        // Assert: Should get a 201 response and the task should be in the database
        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'New Task')
            ->assertJsonPath('data.priority', 'high');

        $this->assertDatabaseHas('tasks', [
            'title' => 'New Task',
            'user_id' => $this->user->id,
        ]);
    }

    #[Test]
    public function it_validates_task_creation_data()
    {
        // Arrange: Log in
        Sanctum::actingAs($this->user);

        // Act: Try to create a task with no data
        $response = $this->postJson('/api/tasks', []);

        // Assert: Should get a 422 response and a validation error for title
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    #[Test]
    public function it_can_update_a_task()
    {
        // Arrange: Log in and create a task
        Sanctum::actingAs($this->user);
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        // Act: Update the task
        $response = $this->putJson("/api/tasks/{$task->id}", [
            'title' => 'Updated Title',
            'status' => 'completed',
        ]);

        // Assert: Should get a 200 response and the task should be updated
        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'Updated Title')
            ->assertJsonPath('data.status', 'completed');
    }

    #[Test]
    public function it_can_delete_a_task()
    {
        // Arrange: Log in and create a task
        Sanctum::actingAs($this->user);
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        // Act: Delete the task
        $response = $this->deleteJson("/api/tasks/{$task->id}");

        // Assert: Should get a 200 response and the task should be gone from the database
        $response->assertStatus(200);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    #[Test]
    public function it_can_reorder_tasks()
    {
        // Arrange: Log in and create two tasks
        Sanctum::actingAs($this->user);
        $task1 = Task::factory()->create(['user_id' => $this->user->id, 'order' => 0]);
        $task2 = Task::factory()->create(['user_id' => $this->user->id, 'order' => 1]);

        // Act: Reorder the tasks
        $response = $this->postJson('/api/tasks/reorder', [
            'tasks' => [
                $task2->id => 0,
                $task1->id => 1,
            ],
        ]);

        // Assert: Should get a 200 response and the tasks should have new order values
        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks', ['id' => $task2->id, 'order' => 0]);
        $this->assertDatabaseHas('tasks', ['id' => $task1->id, 'order' => 1]);
    }
}
