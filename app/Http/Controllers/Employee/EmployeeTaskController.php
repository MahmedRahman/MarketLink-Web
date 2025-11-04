<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\PlanTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeTaskController extends Controller
{
    /**
     * Display the specified task.
     */
    public function show($taskId)
    {
        $employee = Auth::guard('employee')->user();
        
        if (!$employee) {
            return redirect()->route('employee.login')
                ->with('error', 'يرجى تسجيل الدخول للمتابعة');
        }
        
        $task = PlanTask::with(['monthlyPlan.project', 'goal', 'assignedEmployee', 'files', 'comments'])
            ->findOrFail($taskId);

        // التحقق من الصلاحيات: إما المهمة مخصصة له أو هو مدير على المشروع
        $isAssigned = $task->assigned_to === $employee->id;
        
        // إعادة تحميل الموظف مع العلاقات
        $employee->load('projects');
        $isManager = false;
        if ($task->monthlyPlan && $task->monthlyPlan->project_id) {
            $isManager = $employee->isManagerOfProject($task->monthlyPlan->project_id);
        }

        if (!$isAssigned && !$isManager) {
            abort(403, 'ليس لديك صلاحية للوصول إلى هذه المهمة');
        }

        return view('employee.tasks.show', compact('task', 'isManager'));
    }

    /**
     * Show the form for editing the specified task.
     */
    public function edit($taskId)
    {
        $employee = Auth::guard('employee')->user();
        
        if (!$employee) {
            return redirect()->route('employee.login')
                ->with('error', 'يرجى تسجيل الدخول للمتابعة');
        }
        
        $task = PlanTask::with(['monthlyPlan.project', 'goal', 'assignedEmployee'])
            ->findOrFail($taskId);

        // التحقق من الصلاحيات: إما المهمة مخصصة له أو هو مدير على المشروع
        $isAssigned = $task->assigned_to === $employee->id;
        
        // إعادة تحميل الموظف مع العلاقات للتأكد من أن العلاقة projects محملة
        $employee->load('projects');
        $isManager = false;
        if ($task->monthlyPlan && $task->monthlyPlan->project_id) {
            $isManager = $employee->isManagerOfProject($task->monthlyPlan->project_id);
        }

        if (!$isAssigned && !$isManager) {
            abort(403, 'ليس لديك صلاحية لتعديل هذه المهمة');
        }

        // جلب الموظفين للمشروع إذا كان مدير
        $employees = collect();
        if ($isManager && $task->monthlyPlan) {
            $employees = $task->monthlyPlan->employees()->orderBy('name')->get();
        }

        return view('employee.tasks.edit', compact('task', 'isManager', 'employees'));
    }

    /**
     * Update the specified task.
     */
    public function update(Request $request, $taskId)
    {
        $employee = Auth::guard('employee')->user();
        
        if (!$employee) {
            return redirect()->route('employee.login')
                ->with('error', 'يرجى تسجيل الدخول للمتابعة');
        }
        
        $task = PlanTask::with('monthlyPlan')->findOrFail($taskId);

        // التحقق من الصلاحيات
        $isAssigned = $task->assigned_to === $employee->id;
        
        // إعادة تحميل الموظف مع العلاقات
        $employee->load('projects');
        $isManager = false;
        if ($task->monthlyPlan && $task->monthlyPlan->project_id) {
            $isManager = $employee->isManagerOfProject($task->monthlyPlan->project_id);
        }

        if (!$isAssigned && !$isManager) {
            abort(403, 'ليس لديك صلاحية لتعديل هذه المهمة');
        }

        // إذا كان مدير، يمكنه تعديل المزيد من الحقول
        if ($isManager) {
            $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'status' => 'required|in:todo,in_progress,review,done',
                'description' => 'nullable|string',
                'assigned_to' => 'nullable|exists:employees,id',
                'due_date' => 'nullable|date',
            ]);

            $task->update([
                'title' => $request->title ?? $task->title,
                'status' => $request->status,
                'description' => $request->description,
                'assigned_to' => $request->has('assigned_to') ? ($request->assigned_to ?: null) : $task->assigned_to,
                'due_date' => $request->due_date ?: $task->due_date,
            ]);
        } else {
            // الموظف العادي يمكنه تعديل الحالة والوصف فقط
            $request->validate([
                'status' => 'required|in:todo,in_progress,review,done',
                'description' => 'nullable|string',
            ]);

            $task->update([
                'status' => $request->status,
                'description' => $request->description,
            ]);
        }

        return redirect()->route('employee.dashboard')
            ->with('success', 'تم تحديث المهمة بنجاح');
    }

    /**
     * Delete the specified task.
     */
    public function destroy($taskId)
    {
        $employee = Auth::guard('employee')->user();
        
        $task = PlanTask::with('monthlyPlan')->findOrFail($taskId);

        // فقط المدير يمكنه حذف المهام
        $isManager = $task->monthlyPlan ? $employee->isManagerOfProject($task->monthlyPlan->project_id) : false;

        if (!$isManager) {
            abort(403, 'ليس لديك صلاحية لحذف هذه المهمة');
        }

        $task->delete();

        return redirect()->back()
            ->with('success', 'تم حذف المهمة بنجاح');
    }
}
