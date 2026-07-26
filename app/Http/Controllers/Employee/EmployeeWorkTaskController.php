<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\PlanTask;
use App\Models\WorkTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeWorkTaskController extends Controller
{
    /**
     * قائمة مهام الموظف (مساحة العمل + خطط شهرية إن وجدت).
     */
    public function index(Request $request)
    {
        $employee = Auth::guard('employee')->user();
        $status = $request->get('status');

        $workQuery = WorkTask::forEmployee($employee->id)->with('activity');
        if ($status && array_key_exists($status, WorkTask::statuses())) {
            $workQuery->where('status', $status);
        }

        $workTasks = $workQuery
            ->orderByRaw("CASE WHEN status = 'done' THEN 1 ELSE 0 END")
            ->orderBy('due_date')
            ->latest()
            ->get();

        $planQuery = PlanTask::where('assigned_to', $employee->id)
            ->with(['monthlyPlan.project', 'goal']);
        if ($status) {
            $planQuery->where('status', $status);
        }
        $planTasks = $planQuery->orderBy('created_at', 'desc')->get();

        $workBase = fn () => WorkTask::forEmployee($employee->id);

        $stats = [
            'total' => $workBase()->count()
                + PlanTask::where('assigned_to', $employee->id)->count(),
            'todo' => $workBase()->where('status', 'todo')->count()
                + PlanTask::where('assigned_to', $employee->id)->where('status', 'todo')->count(),
            'in_progress' => $workBase()->where('status', 'in_progress')->count()
                + PlanTask::where('assigned_to', $employee->id)->where('status', 'in_progress')->count(),
            'review' => $workBase()->where('status', 'review')->count()
                + PlanTask::where('assigned_to', $employee->id)->where('status', 'review')->count(),
            'done' => $workBase()->where('status', 'done')->count()
                + PlanTask::where('assigned_to', $employee->id)->where('status', 'done')->count(),
            'overdue' => $workBase()
                ->where('status', '!=', 'done')
                ->whereDate('due_date', '<', now()->toDateString())
                ->count(),
        ];

        return view('employee.work.index', [
            'workTasks' => $workTasks,
            'planTasks' => $planTasks,
            'stats' => $stats,
            'statuses' => WorkTask::statuses(),
            'filterStatus' => $status,
            'employee' => $employee,
        ]);
    }

    public function show(WorkTask $task)
    {
        $employee = Auth::guard('employee')->user();
        abort_unless($task->isVisibleToEmployee($employee->id), 403);

        $task->load(['activity', 'contentWriter', 'designer', 'assignedEmployee']);

        return view('employee.work.show', [
            'task' => $task,
            'statuses' => WorkTask::statuses(),
        ]);
    }

    public function updateStatus(Request $request, WorkTask $task)
    {
        $employee = Auth::guard('employee')->user();
        abort_unless($task->isVisibleToEmployee($employee->id), 403);

        $validated = $request->validate([
            'status' => 'required|in:todo,in_progress,review,done',
            'notes' => 'nullable|string',
        ]);

        $task->update($validated);

        return redirect()->route('employee.work.show', $task)->with('success', 'تم تحديث حالة المهمة');
    }
}
