<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_xx_xx_create_schedules_table.php
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade'); // Task reference
            $table->foreignId('staff_id')->constrained('users')->onDelete('cascade'); // Assigned staff
            $table->date('scheduled_date'); // Date for the task
            $table->enum('status', ['Pending', 'In Progress', 'Completed'])->default('Pending'); // Task status
            $table->string('proof_of_completion')->nullable(); // Path to proof of completion image
            $table->timestamp('completed_at')->nullable(); // Timestamp when task is completed
            $table->timestamps();
        });

    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
