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
    /**
     * عرض صفحة إضافة مهمة جديدة
     */
    public function create(Request $request, MonthlyPlan $monthlyPlan): View
    {
        if ($monthlyPlan->organization_id !== $request->user()->organization_id) {
            abort(403);
        }

        $organizationId = $request->user()->organization_id;
        $employees = Employee::where('organization_id', $organizationId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('monthly-plans.tasks.create', compact('monthlyPlan', 'employees'));
    }

    public function store(Request $request, MonthlyPlan $monthlyPlan)
    {
        if ($monthlyPlan->organization_id !== $request->user()->organization_id) {
            if ($request->expectsJson() || $request->header('Content-Type') === 'application/json') {
                return response()->json(['error' => 'غير مصرح'], 403);
            }
            abort(403);
        }

        // تحديد list_type بناءً على assigned_to إذا لم يتم إرساله
        $listType = $request->list_type;
        if (!$listType) {
            $listType = $request->assigned_to ? 'employee' : 'tasks';
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:employees,id',
            'due_date' => 'nullable|date',
            'color' => ['nullable', 'string', 'regex:/^#[A-Fa-f0-9]{6}$/'],
            'status' => 'nullable|in:todo,in_progress,review,done',
        ]);

        // الحصول على آخر ترتيب في القائمة
        $maxOrder = PlanTask::where('monthly_plan_id', $monthlyPlan->id)
            ->where('list_type', $listType)
            ->when($listType === 'employee', function($q) use ($request) {
                $q->where('assigned_to', $request->assigned_to);
            })
            ->max('order') ?? 0;

        $task = PlanTask::create([
            'monthly_plan_id' => $monthlyPlan->id,
            'assigned_to' => $request->assigned_to,
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status ?? 'todo',
            'list_type' => $listType,
            'order' => $maxOrder + 1,
            'due_date' => $request->due_date,
            'color' => $request->color ?? '#6366f1',
        ]);

        $task->load('assignedEmployee');

        // للطلبات من صفحات HTML (form submissions)، نعيد redirect
        $contentType = trim($request->header('Content-Type', ''));
        if ($contentType === 'application/json' || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'task' => $task,
            ]);
        }

        // Default: redirect للصفحة الرئيسية للخطة (للـ form submissions العادية)
        return redirect()->route('monthly-plans.show', $monthlyPlan)
            ->with('success', 'تم إضافة المهمة بنجاح');
    }

    /**
     * الخطوة 2: عرض صفحة تعديل المهمة
     */
    public function edit(Request $request, MonthlyPlan $monthlyPlan, $taskId): View
    {
        // التحقق من الصلاحيات
        if ($monthlyPlan->organization_id !== $request->user()->organization_id) {
            abort(403);
        }

        // الحصول على المهمة
        $task = PlanTask::findOrFail($taskId);

        // التحقق من أن المهمة تتبع هذه الخطة
        if ($task->monthly_plan_id !== $monthlyPlan->id) {
            abort(404);
        }

        // الحصول على الموظفين
        $organizationId = $request->user()->organization_id;
        $employees = Employee::where('organization_id', $organizationId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        // تحميل الموظف المخصص
        $task->load('assignedEmployee');

        return view('monthly-plans.tasks.edit', compact('monthlyPlan', 'task', 'employees'));
    }

    /**
     * الخطوة 3: تحديث المهمة - بسيط في البداية (بدون التعامل مع list_type)
     */
    public function update(Request $request, MonthlyPlan $monthlyPlan, $taskId)
    {
        try {
            // التحقق من الصلاحيات
            if ($monthlyPlan->organization_id !== $request->user()->organization_id) {
                abort(403);
            }

            // الحصول على المهمة
            $task = PlanTask::findOrFail($taskId);

            // التحقق من أن المهمة تتبع هذه الخطة
            if ($task->monthly_plan_id !== $monthlyPlan->id) {
                abort(404);
            }

            // التحقق من البيانات
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'assigned_to' => 'nullable|exists:employees,id',
                'due_date' => 'nullable|date',
                'color' => ['nullable', 'string', 'regex:/^#[A-Fa-f0-9]{6}$/'],
                'status' => 'required|in:todo,in_progress,review,done',
            ]);

            // تحديث البيانات البسيطة فقط (بدون list_type أو order)
            $task->update([
                'title' => $request->title,
                'description' => $request->description,
                'assigned_to' => $request->assigned_to ?: null,
                'due_date' => $request->due_date ?: null,
                'status' => $request->status,
                'color' => $request->color ?? '#6366f1',
            ]);

            // تحميل الموظف المخصص
            $task->load('assignedEmployee');

            // للطلبات JSON
            if ($request->expectsJson() || $request->header('Content-Type') === 'application/json') {
                return response()->json([
                    'success' => true,
                    'task' => $task,
                ]);
            }

            // Redirect للصفحة الرئيسية
            return redirect()->route('monthly-plans.show', $monthlyPlan)
                ->with('success', 'تم تحديث المهمة بنجاح');

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->header('Content-Type') === 'application/json') {
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
            if ($request->expectsJson() || $request->header('Content-Type') === 'application/json') {
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

    public function destroy(Request $request, MonthlyPlan $monthlyPlan, $taskId)
    {
        // Resolve PlanTask manually
        $task = PlanTask::findOrFail($taskId);
        
        if ($monthlyPlan->organization_id !== $request->user()->organization_id) {
            if ($request->expectsJson() || $request->header('Content-Type') === 'application/json') {
                return response()->json(['error' => 'غير مصرح'], 403);
            }
            abort(403);
        }

        if ($task->monthly_plan_id !== $monthlyPlan->id) {
            if ($request->expectsJson() || $request->header('Content-Type') === 'application/json') {
                return response()->json(['error' => 'المهمة غير مرتبطة بهذه الخطة'], 404);
            }
            abort(404);
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

        // للطلبات من صفحات HTML (form submissions)، نعيد redirect
        $contentType = trim($request->header('Content-Type', ''));
        if ($contentType === 'application/json' || $request->expectsJson()) {
            return response()->json([
                'success' => true,
            ]);
        }

        // Default: redirect للصفحة الرئيسية للخطة (للـ form submissions العادية)
        return redirect()->route('monthly-plans.show', $monthlyPlan)
            ->with('success', 'تم حذف المهمة بنجاح');
    }
}
