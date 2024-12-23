<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        return Schedule::with(['task', 'staff'])->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'staff_id' => 'required|exists:users,id',
            'scheduled_date' => 'required|date',
            'status' => 'in:Pending,In Progress,Completed',
        ]);

        return Schedule::create($validated);
    }

    public function update(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'status' => 'in:Pending,In Progress,Completed',
        ]);

        $schedule->update($validated);
        return $schedule;
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();
        return response()->json(['message' => 'Schedule deleted']);
    }
    public function mySchedules(Request $request)
    {
        return Schedule::with('task')
            ->where('staff_id', $request->user()->id)
            ->get();
    }
    public function uploadProof(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'proof' => 'required|image|max:2048', // Validate image file
        ]);

        $proofPath = $request->file('proof')->store('proofs', 'public');

        $schedule->update([
            'proof_of_completion' => $proofPath,
            'status' => 'Completed',
            'completed_at' => now(), // Add a timestamp for completion
        ]);

        return response()->json(['message' => 'Proof uploaded successfully.']);
    }


}
