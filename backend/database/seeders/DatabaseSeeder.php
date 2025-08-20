<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Make an admin user
        $admin = \App\Models\User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'is_admin' => true,
        ]);

        // Make 5 regular users
        $users = \App\Models\User::factory(5)->create([
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]);

        // Make a demo user
        $demoUser = \App\Models\User::create([
            'name' => 'Demo User',
            'email' => 'demo@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('demo123'),
            'is_admin' => false,
        ]);

        // For each regular user, make 5-15 tasks
        foreach ($users as $index => $user) {
            \App\Models\Task::factory(rand(5, 15))->create([
                'user_id' => $user->id,
                // Set the order to the number of tasks the user already has
                'order' => fn() => \App\Models\Task::where('user_id', $user->id)->count(),
            ]);
        }

        // For the demo user, make 10 tasks with different priorities and statuses
        $priorities = ['low', 'medium', 'high'];
        $statuses = ['pending', 'completed'];

        for ($i = 0; $i < 10; $i++) {
            \App\Models\Task::create([
                'title' => "Demo Task " . ($i + 1),
                'description' => "This is demo task number " . ($i + 1),
                'status' => $statuses[array_rand($statuses)],
                'priority' => $priorities[array_rand($priorities)],
                'order' => $i,
                'user_id' => $demoUser->id,
            ]);
        }

        // For the admin, make 5 tasks
        for ($i = 0; $i < 5; $i++) {
            \App\Models\Task::create([
                'title' => "Admin Task " . ($i + 1),
                'description' => "Administrative task",
                'status' => 'pending',
                'priority' => 'high',
                'order' => $i,
                'user_id' => $admin->id,
            ]);
        }
    }
}
