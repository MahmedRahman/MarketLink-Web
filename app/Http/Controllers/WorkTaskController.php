<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\WorkActivity;
use App\Models\WorkTask;
use Illuminate\Http\Request;

class WorkTaskController extends Controller
{
    public function store(Request $request, WorkActivity $work)
    {
        $this->authorizeActivity($request, $work);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'idea' => 'nullable|string',
            'notes' => 'nullable|string',
            'kind' => 'required|in:design,video,content,publish,other',
            'assigned_to' => 'nullable|exists:employees,id',
            'due_date' => 'nullable|date',
        ]);

        // اقتراح الموظف تلقائيًا حسب الدور إذا لم يُختر أحد
        if (empty($validated['assigned_to'])) {
            $validated['assigned_to'] = $this->suggestAssignee($request, $validated['kind']);
        } else {
            $this->ensureEmployeeInOrg($request, $validated['assigned_to']);
        }

        $validated['work_activity_id'] = $work->id;
        $validated['status'] = 'todo';
        $validated['order'] = ($work->tasks()->max('order') ?? 0) + 1;

        WorkTask::create($validated);

        return redirect()->route('work.show', $work)->with('success', 'تمت إضافة المهمة');
    }

    public function update(Request $request, WorkActivity $work, WorkTask $task)
    {
        $this->authorizeActivity($request, $work);
        $this->authorizeTask($work, $task);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'idea' => 'nullable|string',
            'notes' => 'nullable|string',
            'kind' => 'required|in:design,video,content,publish,other',
            'assigned_to' => 'nullable|exists:employees,id',
            'status' => 'required|in:todo,in_progress,review,done',
            'due_date' => 'nullable|date',
        ]);

        if (! empty($validated['assigned_to'])) {
            $this->ensureEmployeeInOrg($request, $validated['assigned_to']);
        }

        $task->update($validated);

        return redirect()->route('work.show', $work)->with('success', 'تم تحديث المهمة');
    }

    public function assign(Request $request, WorkActivity $work, WorkTask $task)
    {
        $this->authorizeActivity($request, $work);
        $this->authorizeTask($work, $task);

        $validated = $request->validate([
            'assigned_to' => 'nullable|exists:employees,id',
        ]);

        if (! empty($validated['assigned_to'])) {
            $this->ensureEmployeeInOrg($request, $validated['assigned_to']);
        }

        $task->update(['assigned_to' => $validated['assigned_to'] ?? null]);

        return back()->with('success', 'تم تحديث التعيين');
    }

    public function destroy(Request $request, WorkActivity $work, WorkTask $task)
    {
        $this->authorizeActivity($request, $work);
        $this->authorizeTask($work, $task);

        $task->delete();

        return redirect()->route('work.show', $work)->with('success', 'تم حذف المهمة');
    }

    /**
     * يقترح موظفًا بناءً على نوع المهمة ودور الموظف.
     */
    private function suggestAssignee(Request $request, string $kind): ?int
    {
        return WorkTask::suggestAssigneeId($request->user()->organization_id, $kind);
    }

    private function ensureEmployeeInOrg(Request $request, int $employeeId): void
    {
        $ok = Employee::where('id', $employeeId)
            ->where('organization_id', $request->user()->organization_id)
            ->exists();
        abort_unless($ok, 403);
    }

    private function authorizeActivity(Request $request, WorkActivity $work): void
    {
        abort_unless($work->organization_id === $request->user()->organization_id, 403);
    }

    private function authorizeTask(WorkActivity $work, WorkTask $task): void
    {
        abort_unless($task->work_activity_id === $work->id, 404);
    }
}
