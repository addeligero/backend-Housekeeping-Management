<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScheduleFactory extends Factory
{
    public function definition()
    {
        return [
            'task_id' => Task::factory(),
            'staff_id' => User::factory()->state(['is_admin' => false]),
            'scheduled_date' => $this->faker->date(),
            'status' => $this->faker->randomElement(['Pending', 'In Progress', 'Completed']),
        ];
    }
}
