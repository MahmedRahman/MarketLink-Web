<?php

namespace App\Http\Controllers;

use App\Models\PlanTask;
use App\Models\MonthlyPlan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

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
        ]);

        $task->load('assignedEmployee');

        return response()->json([
            'success' => true,
            'task' => $task,
        ]);
    }

    public function update(Request $request, MonthlyPlan $monthlyPlan, PlanTask $task)
    {
        if ($monthlyPlan->organization_id !== $request->user()->organization_id) {
            return response()->json(['error' => 'غير مصرح'], 403);
        }

        if ($task->monthly_plan_id !== $monthlyPlan->id) {
            return response()->json(['error' => 'المهمة غير مرتبطة بهذه الخطة'], 404);
        }

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:employees,id',
            'due_date' => 'nullable|date',
            'status' => 'sometimes|in:todo,in_progress,review,done',
            'list_type' => 'sometimes|in:tasks,employee',
        ]);

        $updateData = $request->only([
            'title', 'description', 'assigned_to', 'due_date', 'status'
        ]);

        // إذا تم تغيير assigned_to، تحديث list_type
        if ($request->has('assigned_to')) {
            $updateData['list_type'] = $request->assigned_to ? 'employee' : 'tasks';
        } elseif ($request->has('list_type')) {
            $updateData['list_type'] = $request->list_type;
        }

        $task->update($updateData);

        $task->load('assignedEmployee');

        return response()->json([
            'success' => true,
            'task' => $task,
        ]);
    }

    public function move(Request $request, MonthlyPlan $monthlyPlan, PlanTask $task): JsonResponse
    {
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

    public function destroy(Request $request, MonthlyPlan $monthlyPlan, PlanTask $task): JsonResponse
    {
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
