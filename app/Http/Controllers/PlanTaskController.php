<?php

namespace App\Http\Controllers;

use App\Models\PlanTask;
use App\Models\MonthlyPlan;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PlanTaskController extends Controller
{
    public function store(Request $request, MonthlyPlan $monthlyPlan)
    {
        if ($monthlyPlan->organization_id !== $request->user()->organization_id) {
            return response()->json(['error' => 'غير مصرح'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:employees,id',
            'due_date' => 'nullable|date',
            'color' => ['nullable', 'string', 'regex:/^#[A-Fa-f0-9]{6}$/'],
            'list_type' => 'required|in:tasks,employee',
        ]);

        // الحصول على آخر ترتيب في القائمة
        $maxOrder = PlanTask::where('monthly_plan_id', $monthlyPlan->id)
            ->where('list_type', $request->list_type)
            ->when($request->list_type === 'employee', function($q) use ($request) {
                $q->where('assigned_to', $request->assigned_to);
            })
            ->max('order') ?? 0;

        $task = PlanTask::create([
            'monthly_plan_id' => $monthlyPlan->id,
            'assigned_to' => $request->assigned_to,
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'todo',
            'list_type' => $request->list_type,
            'order' => $maxOrder + 1,
            'due_date' => $request->due_date,
            'color' => $request->color ?? '#6366f1',
        ]);

        $task->load('assignedEmployee');

        return response()->json([
            'success' => true,
            'task' => $task,
        ]);
    }

    /**
     * عرض صفحة تعديل المهمة
     */
    public function edit(Request $request, MonthlyPlan $monthlyPlan, $taskId): View
    {
        if ($monthlyPlan->organization_id !== $request->user()->organization_id) {
            abort(403);
        }

        $task = PlanTask::findOrFail($taskId);

        if ($task->monthly_plan_id !== $monthlyPlan->id) {
            abort(404);
        }

        $organizationId = $request->user()->organization_id;
        $employees = Employee::where('organization_id', $organizationId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $task->load('assignedEmployee');

        return view('monthly-plans.tasks.edit', compact('monthlyPlan', 'task', 'employees'));
    }

    public function update(Request $request, MonthlyPlan $monthlyPlan, $taskId)
    {
        try {
            // Resolve PlanTask manually
            $task = PlanTask::findOrFail($taskId);
            
            if ($monthlyPlan->organization_id !== $request->user()->organization_id) {
                if ($request->expectsJson() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'error' => 'غير مصرح'], 403);
                }
                abort(403);
            }

            if ($task->monthly_plan_id !== $monthlyPlan->id) {
                if ($request->expectsJson() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'error' => 'المهمة غير مرتبطة بهذه الخطة'], 404);
                }
                abort(404);
            }

            // Different validation rules for JSON vs form requests
            $validationRules = [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'assigned_to' => 'nullable|exists:employees,id',
                'due_date' => 'nullable|date',
                'color' => ['nullable', 'string', 'regex:/^#[A-Fa-f0-9]{6}$/'],
                'status' => 'required|in:todo,in_progress,review,done',
            ];

            $request->validate($validationRules);

            $updateData = [
                'title' => $request->title,
                'description' => $request->description,
                'assigned_to' => $request->assigned_to,
                'due_date' => $request->due_date ?: null,
                'status' => $request->status,
                'color' => $request->color ?? '#6366f1',
                'list_type' => $request->assigned_to ? 'employee' : 'tasks',
            ];

            $task->update($updateData);

            $task->load('assignedEmployee');

            // إذا كان الطلب JSON (من AJAX) أرجع JSON، وإلا redirect
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'task' => $task,
                ]);
            }

            return redirect()->route('monthly-plans.show', $monthlyPlan)
                ->with('success', 'تم تحديث المهمة بنجاح');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'خطأ في التحقق من البيانات',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'حدث خطأ أثناء تحديث المهمة: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تحديث المهمة: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function move(Request $request, MonthlyPlan $monthlyPlan, $taskId): JsonResponse
    {
        // Resolve PlanTask manually
        $task = PlanTask::findOrFail($taskId);
        
        if ($monthlyPlan->organization_id !== $request->user()->organization_id) {
            return response()->json(['error' => 'غير مصرح'], 403);
        }

        $request->validate([
            'list_type' => 'required|in:tasks,employee',
            'assigned_to' => 'nullable|exists:employees,id',
            'new_order' => 'required|integer|min:0',
        ]);

        try {
            $oldListType = $task->list_type;
            $oldAssignedTo = $task->assigned_to;
            $oldOrder = $task->order;

            $newListType = $request->list_type;
            $newAssignedTo = $request->assigned_to;
            $newOrder = $request->new_order;

            // تحديث ترتيب المهام في القائمة القديمة
            PlanTask::where('monthly_plan_id', $monthlyPlan->id)
                ->where('list_type', $oldListType)
                ->when($oldListType === 'employee', function($q) use ($oldAssignedTo) {
                    $q->where('assigned_to', $oldAssignedTo);
                })
                ->where('order', '>', $oldOrder)
                ->decrement('order');

            // تحديث ترتيب المهام في القائمة الجديدة
            PlanTask::where('monthly_plan_id', $monthlyPlan->id)
                ->where('list_type', $newListType)
                ->when($newListType === 'employee', function($q) use ($newAssignedTo) {
                    $q->where('assigned_to', $newAssignedTo);
                })
                ->where('order', '>=', $newOrder)
                ->increment('order');

            // تحديث المهمة
            $task->update([
                'list_type' => $newListType,
                'assigned_to' => $newAssignedTo,
                'order' => $newOrder,
            ]);

            $task->load('assignedEmployee');

            return response()->json([
                'success' => true,
                'task' => $task,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'حدث خطأ أثناء نقل المهمة: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Request $request, MonthlyPlan $monthlyPlan, $taskId): JsonResponse
    {
        // Resolve PlanTask manually
        $task = PlanTask::findOrFail($taskId);
        
        if ($monthlyPlan->organization_id !== $request->user()->organization_id) {
            return response()->json(['error' => 'غير مصرح'], 403);
        }

        if ($task->monthly_plan_id !== $monthlyPlan->id) {
            return response()->json(['error' => 'المهمة غير مرتبطة بهذه الخطة'], 404);
        }

        $task->delete();

        // إعادة ترتيب المهام في نفس القائمة
        PlanTask::where('monthly_plan_id', $monthlyPlan->id)
            ->where('list_type', $task->list_type)
            ->when($task->list_type === 'employee', function($q) use ($task) {
                $q->where('assigned_to', $task->assigned_to);
            })
            ->where('order', '>', $task->order)
            ->decrement('order');

        return response()->json([
            'success' => true,
        ]);
    }
}
