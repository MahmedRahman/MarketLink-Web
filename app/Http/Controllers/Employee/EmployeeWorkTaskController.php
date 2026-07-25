<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\WorkTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeWorkTaskController extends Controller
{
    public function show(WorkTask $task)
    {
        $employee = Auth::guard('employee')->user();
        abort_unless($task->assigned_to === $employee->id, 403);

        $task->load('activity');

        return view('employee.work.show', [
            'task' => $task,
            'statuses' => WorkTask::statuses(),
        ]);
    }

    public function updateStatus(Request $request, WorkTask $task)
    {
        $employee = Auth::guard('employee')->user();
        abort_unless($task->assigned_to === $employee->id, 403);

        $validated = $request->validate([
            'status' => 'required|in:todo,in_progress,review,done',
            'notes' => 'nullable|string',
        ]);

        $task->update($validated);

        return redirect()->route('employee.work.show', $task)->with('success', 'تم تحديث حالة المهمة');
    }
}
