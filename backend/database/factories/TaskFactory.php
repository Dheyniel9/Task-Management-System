<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    // This factory makes Task models
    protected $model = Task::class;

    // This sets up the default fake data for a new Task
    public function definition(): array
    {
        return [
            // The task's title will be a short random sentence
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['pending', 'completed']),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high']),
            'order' => $this->faker->numberBetween(0, 100),
            'user_id' => User::factory(),
        ];
    }

    // Make a task that is always pending
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    // Make a task that is always completed
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }

    // Make a task that always has high priority
    public function highPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'high',
        ]);
    }
}
