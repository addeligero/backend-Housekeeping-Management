<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        return Schedule::with('task', 'staff')
            ->get(['id', 'task_id', 'staff_id', 'room_number', 'scheduled_date', 'status', 'proof_of_completion']);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'staff_id' => 'required|exists:users,id',
            'scheduled_date' => 'required|date',
            'room_number' => 'required|string', // Include validation for room_number
            'status' => 'in:Pending,In Progress,Completed|nullable',
        ]);

        return Schedule::create(array_merge($validated, ['status' => 'Pending']));
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
        $user = $request->user();
        return Schedule::with('task')
            ->where('staff_id', $user->id)
            ->get(['id', 'task_id', 'room_number', 'scheduled_date', 'status', 'proof_of_completion']);
    }

    public function uploadProof(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);

        // Validate the uploaded file
        $request->validate([
            'proof' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Ensure it's an image
        ]);

        if ($request->hasFile('proof')) {
            $file = $request->file('proof');
            $path = $file->store('proofs', 'public'); // Save in storage/app/public/proofs
            $schedule->proof_of_completion = $path; // Save the file path to the DB
            $schedule->status = 'Completed';
            $schedule->completed_at = now();
            $schedule->save();

            return response()->json(['message' => 'Proof uploaded successfully.', 'schedule' => $schedule]);
        }

        return response()->json(['error' => 'File upload failed.'], 400);
    }

}
