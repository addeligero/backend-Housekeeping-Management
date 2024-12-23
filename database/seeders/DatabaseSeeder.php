<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\Schedule;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        
        User::factory(5)->create(); // Create 5 staff users

        // Create tasks and schedules
        Task::factory(10)->create(); // Create 10 tasks

        Schedule::factory(10)->create(); // Assign schedules to tasks and staff

    }
}
