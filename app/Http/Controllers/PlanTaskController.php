<?php

namespace App\Http\Controllers;

use App\Models\PlanTask;
use App\Models\PlanTaskFile;
use App\Models\PlanTaskComment;
use App\Models\MonthlyPlan;
use App\Models\MonthlyPlanGoal;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
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

        // جلب الموظفين المرتبطين بهذه الخطة الشهرية فقط
        $employees = $monthlyPlan->employees()
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        // جلب الأهداف المرتبطة بهذه الخطة الشهرية
        $goals = $monthlyPlan->goals()->orderBy('goal_name')->get();

        return view('monthly-plans.tasks.create', compact('monthlyPlan', 'employees', 'goals'));
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
        
        // التأكد من أن list_type يتوافق مع assigned_to
        if ($listType === 'employee' && !$request->assigned_to) {
            $listType = 'tasks';
        }
        if ($listType === 'tasks' && $request->assigned_to) {
            $listType = 'employee';
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'idea' => 'nullable|string',
            'assigned_to' => 'nullable|exists:employees,id',
            'list_type' => 'nullable|in:tasks,employee,ready,publish',
            'due_date' => 'nullable|date',
            'color' => ['nullable', 'string', 'regex:/^#[A-Fa-f0-9]{6}$/'],
            'status' => 'nullable|in:todo,in_progress,review,done,publish,archived',
            'links' => 'nullable|array',
            'links.*.title' => 'nullable|string|max:255',
            'links.*.url' => 'nullable|url|max:500',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,zip,rar', // 10MB max
            'goal_id' => ['nullable', 'sometimes', function ($attribute, $value, $fail) use ($monthlyPlan) {
                if (empty($value)) {
                    return;
                }
                $goal = MonthlyPlanGoal::where('id', $value)
                    ->where('monthly_plan_id', $monthlyPlan->id)
                    ->exists();
                if (!$goal) {
                    $fail('الهدف المحدد غير صحيح.');
                }
            }],
        ]);

        // التحقق من أن الهدف ينتمي لهذه الخطة
        if ($request->goal_id && $request->goal_id !== '') {
            $goal = MonthlyPlanGoal::where('id', $request->goal_id)
                ->where('monthly_plan_id', $monthlyPlan->id)
                ->first();
            if (!$goal) {
                if ($request->expectsJson() || $request->header('Content-Type') === 'application/json') {
                    return response()->json([
                        'success' => false,
                        'error' => 'الهدف المحدد غير صحيح',
                        'errors' => ['goal_id' => ['الهدف المحدد غير صحيح']]
                    ], 422);
                }
                return redirect()->back()->withErrors(['goal_id' => 'الهدف المحدد غير صحيح'])->withInput();
            }
        }

        // الحصول على آخر ترتيب في القائمة
        $maxOrder = PlanTask::where('monthly_plan_id', $monthlyPlan->id)
            ->where('list_type', $listType)
            ->when(in_array($listType, ['employee']), function($q) use ($request) {
                $q->where('assigned_to', $request->assigned_to);
            })
            ->max('order') ?? 0;

        // تجهيز الروابط للـ task_data
        $taskData = [];
        if ($request->has('links') && is_array($request->links)) {
            $links = [];
            foreach ($request->links as $link) {
                if (!empty($link['url']) && filter_var($link['url'], FILTER_VALIDATE_URL)) {
                    $links[] = [
                        'title' => $link['title'] ?? '',
                        'url' => $link['url'],
                    ];
                }
            }
            if (!empty($links)) {
                $taskData['links'] = $links;
            }
        }

        $task = PlanTask::create([
            'monthly_plan_id' => $monthlyPlan->id,
            'goal_id' => $request->goal_id && $request->goal_id !== '' ? $request->goal_id : null,
            'assigned_to' => $request->assigned_to,
            'title' => $request->title,
            'description' => $request->description,
            'idea' => $request->idea,
            'status' => $request->status ?? 'todo',
            'list_type' => $listType,
            'order' => $maxOrder + 1,
            'due_date' => $request->due_date,
            'color' => $request->color ?? '#6366f1',
            'task_data' => !empty($taskData) ? $taskData : null,
        ]);

        // تحديث achieved_value إذا كانت المهمة في ready أو publish
        if ($task->goal_id && in_array($listType, ['ready', 'publish'])) {
            $this->updateGoalAchievedValue($task->goal_id);
        }

        // رفع المرفقات إن وجدت
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                if ($file->isValid()) {
                    $fileName = $file->getClientOriginalName();
                    $filePath = $file->store('task_files/' . $task->id, 'public');
                    $fileType = $file->getClientMimeType();
                    $fileSize = $file->getSize();

                    PlanTaskFile::create([
                        'plan_task_id' => $task->id,
                        'file_name' => $fileName,
                        'file_path' => $filePath,
                        'file_type' => $fileType,
                        'file_size' => $fileSize,
                    ]);
                }
            }
        }

        $task->load('assignedEmployee', 'files', 'monthlyPlan.project');

        // إرسال webhook إذا كانت المهمة مخصصة لموظف
        if ($task->assigned_to && $task->assignedEmployee) {
            $this->sendTaskWebhook($task);
        }

        // للطلبات من صفحات HTML (form submissions)، نعيد redirect
        $contentType = trim($request->header('Content-Type', ''));
        $acceptHeader = trim($request->header('Accept', ''));
        if ($contentType === 'application/json' || $request->expectsJson() || str_contains($acceptHeader, 'application/json')) {
            return response()->json([
                'success' => true,
                'task' => $task,
                'message' => 'تم إضافة المهمة بنجاح',
            ]);
        }

        // Default: redirect للصفحة الرئيسية للخطة (للـ form submissions العادية)
        return redirect()->route('monthly-plans.show', $monthlyPlan)
            ->with('success', 'تم إضافة المهمة بنجاح');
    }

    /**
     * عرض صفحة تفاصيل المهمة
     */
    public function show(Request $request, MonthlyPlan $monthlyPlan, $taskId): View
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

        // تحميل العلاقات
        $task->load(['assignedEmployee', 'files', 'comments.user', 'goal']);

        return view('monthly-plans.tasks.show', compact('monthlyPlan', 'task'));
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

        // جلب الموظفين المرتبطين بهذه الخطة الشهرية فقط
        $employees = $monthlyPlan->employees()
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        // جلب الأهداف المرتبطة بهذه الخطة الشهرية
        $goals = $monthlyPlan->goals()->orderBy('goal_name')->get();

        // تحميل الموظف المخصص والمرفقات والهدف
        $task->load('assignedEmployee', 'files', 'goal');

        return view('monthly-plans.tasks.edit', compact('monthlyPlan', 'task', 'employees', 'goals'));
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
            // إذا كان الطلب JSON وتم إرسال status فقط، نسمح بالتحديث الجزئي
            $isJsonRequest = $request->expectsJson() || $request->header('Content-Type') === 'application/json';
            $isPartialUpdate = $isJsonRequest && $request->has('status') && !$request->has('title');
            
            $validationRules = [
                'title' => $isPartialUpdate ? 'nullable|string|max:255' : 'required|string|max:255',
                'description' => 'nullable|string',
                'idea' => 'nullable|string',
                'assigned_to' => 'nullable|exists:employees,id',
                'due_date' => 'nullable|date',
                'color' => ['nullable', 'string', 'regex:/^#[A-Fa-f0-9]{6}$/'],
                'status' => $isPartialUpdate ? 'required|in:todo,in_progress,review,done,publish,archived' : 'required|in:todo,in_progress,review,done,publish,archived',
                'links' => 'nullable|array',
                'links.*.title' => 'nullable|string|max:255',
                'links.*.url' => 'nullable|url|max:500',
                'attachments' => 'nullable|array',
                'attachments.*' => 'file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,zip,rar', // 10MB max
                'goal_id' => ['nullable', 'sometimes', function ($attribute, $value, $fail) use ($monthlyPlan) {
                if (empty($value)) {
                    return;
                }
                $goal = MonthlyPlanGoal::where('id', $value)
                    ->where('monthly_plan_id', $monthlyPlan->id)
                    ->exists();
                if (!$goal) {
                    $fail('الهدف المحدد غير صحيح.');
                }
            }],
            ];
            
            $request->validate($validationRules);

            // التحقق من أن الهدف ينتمي لهذه الخطة
            if ($request->goal_id && $request->goal_id !== '') {
                $goal = MonthlyPlanGoal::where('id', $request->goal_id)
                    ->where('monthly_plan_id', $monthlyPlan->id)
                    ->first();
                if (!$goal) {
                    if ($request->expectsJson() || $request->header('Content-Type') === 'application/json') {
                        return response()->json([
                            'success' => false,
                            'error' => 'الهدف المحدد غير صحيح',
                            'errors' => ['goal_id' => ['الهدف المحدد غير صحيح']]
                        ], 422);
                    }
                    return redirect()->back()->withErrors(['goal_id' => 'الهدف المحدد غير صحيح'])->withInput();
                }
            }

            // تجهيز الروابط للـ task_data
            $taskData = $task->task_data ?? [];
            if ($request->has('links') && is_array($request->links)) {
                $links = [];
                foreach ($request->links as $link) {
                    if (!empty($link['url']) && filter_var($link['url'], FILTER_VALIDATE_URL)) {
                        $links[] = [
                            'title' => $link['title'] ?? '',
                            'url' => $link['url'],
                        ];
                    }
                }
                if (!empty($links)) {
                    $taskData['links'] = $links;
                } else {
                    unset($taskData['links']);
                }
            }

            // تحديث البيانات البسيطة فقط (بدون list_type أو order)
            $oldGoalId = $task->goal_id;
            $oldStatus = $task->status;
            $oldListType = $task->list_type;
            $oldAssignedTo = $task->assigned_to;
            
            // تحديث list_type بناءً على الحالة الجديدة
            $newListType = $task->list_type;
            $newAssignedTo = $request->has('assigned_to') ? ($request->assigned_to ?: null) : $task->assigned_to;
            
            // إذا تغيرت الحالة إلى publish أو ready، يجب تحديث list_type
            if ($request->has('status') && in_array($request->status, ['publish', 'ready'])) {
                $newListType = $request->status;
                // إذا كانت الحالة publish أو ready، يجب إزالة assigned_to
                $newAssignedTo = null;
            } elseif ($request->has('status') && $request->status === 'archived') {
                // إذا كانت الحالة archived، نحتفظ بـ list_type الحالي
                $newListType = $task->list_type;
            } elseif ($request->has('assigned_to')) {
                // إذا تم تغيير assigned_to فقط (بدون تغيير الحالة)، نحدد list_type بناءً على ذلك
                if ($newAssignedTo) {
                    $newListType = 'employee';
                } else {
                    $newListType = 'tasks';
                }
            }
            
            // إذا كان تحديث جزئي (مثل تحديث الحالة فقط)، نحدث الحقول المرسلة فقط
            if ($isPartialUpdate) {
                $updateData = [];
                if ($request->has('status')) {
                    $updateData['status'] = $request->status;
                    // تحديث list_type إذا تغيرت الحالة
                    if ($newListType !== $oldListType) {
                        $updateData['list_type'] = $newListType;
                    }
                    // تحديث assigned_to إذا تغيرت الحالة إلى publish أو ready
                    if (in_array($request->status, ['publish', 'ready'])) {
                        $updateData['assigned_to'] = null;
                    }
                }
                if ($request->has('title')) {
                    $updateData['title'] = $request->title;
                }
                if ($request->has('description')) {
                    $updateData['description'] = $request->description;
                }
                if ($request->has('idea')) {
                    $updateData['idea'] = $request->idea;
                }
                if ($request->has('assigned_to')) {
                    $updateData['assigned_to'] = $newAssignedTo;
                    if ($newListType !== $oldListType) {
                        $updateData['list_type'] = $newListType;
                    }
                }
                if ($request->has('due_date')) {
                    $updateData['due_date'] = $request->due_date ?: null;
                }
                if ($request->has('color')) {
                    $updateData['color'] = $request->color;
                }
                if ($request->has('goal_id')) {
                    $updateData['goal_id'] = $request->goal_id && $request->goal_id !== '' ? $request->goal_id : null;
                }
                if ($request->has('links')) {
                    $updateData['task_data'] = !empty($taskData) ? $taskData : null;
                }
                $task->update($updateData);
            } else {
                // تحديث كامل - نستخدم القيم المحسوبة
                $updateData = [
                    'title' => $request->title,
                    'description' => $request->description,
                    'idea' => $request->idea,
                    'assigned_to' => $newAssignedTo,
                    'due_date' => $request->due_date ?: null,
                    'status' => $request->status,
                    'list_type' => $newListType,
                    'color' => $request->color ?? '#6366f1',
                    'goal_id' => $request->goal_id && $request->goal_id !== '' ? $request->goal_id : null,
                    'task_data' => !empty($taskData) ? $taskData : null,
                ];
                $task->update($updateData);
            }
            
            // تحديث achieved_value للهدف إذا تغيرت الحالة أو list_type
            if ($task->goal_id) {
                if ($oldStatus !== $request->status || $oldListType !== $newListType) {
                    if (in_array($request->status, ['ready', 'publish']) || in_array($newListType, ['ready', 'publish']) ||
                        in_array($oldStatus, ['ready', 'publish']) || in_array($oldListType, ['ready', 'publish'])) {
                        $this->updateGoalAchievedValue($task->goal_id);
                    }
                }
            }
            
            // تحديث achieved_value للأهداف القديمة والجديدة
            if ($oldGoalId && $oldGoalId != $task->goal_id) {
                $this->updateGoalAchievedValue($oldGoalId);
            }
            if ($task->goal_id && $task->goal_id != $oldGoalId) {
                $this->updateGoalAchievedValue($task->goal_id);
            }

            // رفع المرفقات الجديدة إن وجدت
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    if ($file->isValid()) {
                        $fileName = $file->getClientOriginalName();
                        $filePath = $file->store('task_files/' . $task->id, 'public');
                        $fileType = $file->getClientMimeType();
                        $fileSize = $file->getSize();

                        PlanTaskFile::create([
                            'plan_task_id' => $task->id,
                            'file_name' => $fileName,
                            'file_path' => $filePath,
                            'file_type' => $fileType,
                            'file_size' => $fileSize,
                        ]);
                    }
                }
            }

            // تحميل الموظف المخصص والمرفقات
            $task->load('assignedEmployee', 'files');

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
            \Log::error('Error updating task: ' . $e->getMessage(), [
                'task_id' => $taskId,
                'monthly_plan_id' => $monthlyPlan->id,
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
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
            'list_type' => 'required|in:tasks,employee,ready,publish',
            'assigned_to' => [
                'nullable',
                function ($attribute, $value, $fail) use ($request) {
                    // إذا كانت list_type هي employee، يجب أن يكون assigned_to موجوداً
                    if ($request->list_type === 'employee' && empty($value)) {
                        $fail('يجب تحديد موظف للقائمة من نوع employee.');
                    }
                    // إذا كانت list_type ليست employee و assigned_to موجود، يجب التحقق من وجود الموظف
                    if (!empty($value) && !\App\Models\Employee::where('id', $value)->exists()) {
                        $fail('الموظف المحدد غير موجود.');
                    }
                }
            ],
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
            $oldListQuery = PlanTask::where('monthly_plan_id', $monthlyPlan->id)
                ->where('list_type', $oldListType);
            
            // إذا كانت القائمة القديمة من نوع employee، نحتاج إلى تصفية حسب assigned_to
            if ($oldListType === 'employee' && $oldAssignedTo) {
                $oldListQuery->where('assigned_to', $oldAssignedTo);
            }
            // إذا كانت القائمة القديمة من نوع tasks أو ready أو publish، يجب أن تكون assigned_to = null
            elseif (in_array($oldListType, ['tasks', 'ready', 'publish'])) {
                $oldListQuery->whereNull('assigned_to');
            }
            
            $oldListQuery->where('order', '>', $oldOrder)->decrement('order');

            // تحديث ترتيب المهام في القائمة الجديدة
            $newListQuery = PlanTask::where('monthly_plan_id', $monthlyPlan->id)
                ->where('list_type', $newListType);
            
            // إذا كانت القائمة الجديدة من نوع employee، نحتاج إلى تصفية حسب assigned_to
            if ($newListType === 'employee' && $newAssignedTo) {
                $newListQuery->where('assigned_to', $newAssignedTo);
            }
            // إذا كانت القائمة الجديدة من نوع tasks أو ready أو publish، يجب أن تكون assigned_to = null
            elseif (in_array($newListType, ['tasks', 'ready', 'publish'])) {
                $newListQuery->whereNull('assigned_to');
            }
            
            $newListQuery->where('order', '>=', $newOrder)->increment('order');

            // تحديث المهمة
            $oldGoalId = $task->goal_id;
            $task->update([
                'list_type' => $newListType,
                'assigned_to' => in_array($newListType, ['ready', 'publish', 'tasks']) ? null : ($newAssignedTo ?: null),
                'order' => $newOrder,
            ]);

            // تحديث achieved_value للهدف إذا كانت المهمة مرتبطة بهدف
            if ($task->goal_id) {
                // إذا انتقلت المهمة إلى ready أو publish، تحديث achieved_value
                if (in_array($newListType, ['ready', 'publish'])) {
                    $this->updateGoalAchievedValue($task->goal_id);
                }
                // إذا انتقلت المهمة من ready أو publish إلى مكان آخر، تحديث achieved_value
                elseif (in_array($oldListType, ['ready', 'publish'])) {
                    $this->updateGoalAchievedValue($task->goal_id);
                }
            }

            // إعادة تحميل المهمة مباشرة من قاعدة البيانات
            $task = PlanTask::findOrFail($taskId);
            $task->load('assignedEmployee:id,name,phone', 'monthlyPlan.project');

            // إرسال webhook إذا تم نقل المهمة إلى موظف (إما لأول مرة أو لموظف مختلف)
            if ($newListType === 'employee' && $newAssignedTo) {
                // إرسال webhook إذا:
                // 1. المهمة لم تكن مخصصة لموظف من قبل (oldListType !== 'employee')
                // 2. أو تم نقلها لموظف مختلف (oldAssignedTo !== newAssignedTo)
                if ($oldListType !== 'employee' || $oldAssignedTo != $newAssignedTo) {
                    $this->sendTaskWebhook($task);
                }
            }

            // بناء array يدوياً لتجنب مشاكل casting
            $taskArray = [
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'status' => $task->status,
                'list_type' => $task->list_type,
                'assigned_to' => $task->assigned_to,
                'due_date' => $task->due_date ? $task->due_date->format('Y-m-d') : null,
                'color' => (string) $task->getRawOriginal('color'),
                'order' => $task->order,
            ];

            if ($task->assignedEmployee) {
                $taskArray['assigned_employee'] = [
                    'id' => $task->assignedEmployee->id,
                    'name' => $task->assignedEmployee->name,
                ];
            }

            return response()->json([
                'success' => true,
                'task' => $taskArray,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'خطأ في التحقق من البيانات',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error moving task: ' . $e->getMessage(), [
                'task_id' => $taskId,
                'old_list_type' => $oldListType ?? null,
                'new_list_type' => $newListType ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'حدث خطأ أثناء نقل المهمة: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Quick assign employee to task
     */
    public function quickAssign(Request $request, MonthlyPlan $monthlyPlan, $taskId): JsonResponse
    {
        // Resolve PlanTask manually
        $task = PlanTask::findOrFail($taskId);
        
        if ($monthlyPlan->organization_id !== $request->user()->organization_id) {
            return response()->json(['error' => 'غير مصرح'], 403);
        }

        if ($task->monthly_plan_id !== $monthlyPlan->id) {
            return response()->json(['error' => 'المهمة غير مرتبطة بهذه الخطة'], 404);
        }

        $request->validate([
            'assigned_to' => 'nullable|exists:employees,id',
            'list_type' => 'required|in:tasks,employee,ready,publish',
        ]);

        try {
            $oldListType = $task->list_type;
            $oldAssignedTo = $task->assigned_to;
            $oldOrder = $task->order;

            $newListType = $request->list_type;
            $newAssignedTo = $request->assigned_to;

            // تحديث المهمة
            $task->list_type = $newListType;
            $task->assigned_to = $newAssignedTo ?: null;
            
            // إذا تم نقل المهمة من قائمة إلى أخرى، نحتاج إلى تحديث الترتيب
            if ($oldListType !== $newListType || $oldAssignedTo != $newAssignedTo) {
                // تحديث ترتيب المهام في القائمة القديمة
                $oldListQuery = PlanTask::where('monthly_plan_id', $monthlyPlan->id)
                    ->where('list_type', $oldListType);
                
                if ($oldListType === 'employee' && $oldAssignedTo) {
                    $oldListQuery->where('assigned_to', $oldAssignedTo);
                } elseif (in_array($oldListType, ['tasks', 'ready', 'publish'])) {
                    $oldListQuery->whereNull('assigned_to');
                }
                
                $oldListQuery->where('order', '>', $oldOrder)->decrement('order');

                // إضافة المهمة في نهاية القائمة الجديدة
                $newListQuery = PlanTask::where('monthly_plan_id', $monthlyPlan->id)
                    ->where('list_type', $newListType);
                
                if ($newListType === 'employee' && $newAssignedTo) {
                    $newListQuery->where('assigned_to', $newAssignedTo);
                } elseif (in_array($newListType, ['tasks', 'ready', 'publish'])) {
                    $newListQuery->whereNull('assigned_to');
                }
                
                $maxOrder = $newListQuery->max('order') ?? 0;
                $task->order = $maxOrder + 1;
            }

            $task->save();

            // إرسال webhook إذا تم تعيين المهمة لموظف
            if ($newListType === 'employee' && $newAssignedTo) {
                if ($oldListType !== 'employee' || $oldAssignedTo != $newAssignedTo) {
                    $task->load('assignedEmployee:id,name,phone', 'monthlyPlan.project');
                    $this->sendTaskWebhook($task);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث الموظف المسؤول بنجاح',
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in quick assign: ' . $e->getMessage(), [
                'task_id' => $taskId,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'حدث خطأ أثناء تحديث الموظف: ' . $e->getMessage(),
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

            $oldListType = $task->list_type;
            $oldAssignedTo = $task->assigned_to;
            $oldOrder = $task->order;
            $oldGoalId = $task->goal_id;

            $task->delete();

            // تحديث achieved_value للهدف إذا كانت المهمة المحذوفة مرتبطة بهدف وكانت في ready أو publish
            if ($oldGoalId && in_array($oldListType, ['ready', 'publish'])) {
                $this->updateGoalAchievedValue($oldGoalId);
            }

            // إعادة ترتيب المهام في نفس القائمة
        $reorderQuery = PlanTask::where('monthly_plan_id', $monthlyPlan->id)
            ->where('list_type', $oldListType);
        
        // إذا كانت القائمة من نوع employee، نحتاج إلى تصفية حسب assigned_to
        if ($oldListType === 'employee' && $oldAssignedTo) {
            $reorderQuery->where('assigned_to', $oldAssignedTo);
        }
        // إذا كانت القائمة من نوع tasks أو ready أو publish، يجب أن تكون assigned_to = null
        elseif (in_array($oldListType, ['tasks', 'ready', 'publish'])) {
            $reorderQuery->whereNull('assigned_to');
        }
        
        $reorderQuery->where('order', '>', $oldOrder)->decrement('order');

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

    /**
     * عرض ملف مرفق بالمهمة في المتصفح
     */
    public function viewFile(Request $request, MonthlyPlan $monthlyPlan, $taskId, $fileId)
    {
        if ($monthlyPlan->organization_id !== $request->user()->organization_id) {
            abort(403);
        }

        $task = PlanTask::findOrFail($taskId);
        if ($task->monthly_plan_id !== $monthlyPlan->id) {
            abort(404);
        }

        $file = PlanTaskFile::findOrFail($fileId);
        if ($file->plan_task_id !== $task->id) {
            abort(404);
        }

        if (!Storage::disk('public')->exists($file->file_path)) {
            abort(404, 'الملف غير موجود');
        }

        // عرض الملف في المتصفح (للصور و PDFs)
        $filePath = Storage::disk('public')->path($file->file_path);
        $mimeType = Storage::disk('public')->mimeType($file->file_path);
        
        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $file->file_name . '"',
        ]);
    }

    /**
     * تحميل ملف مرفق بالمهمة
     */
    public function downloadFile(Request $request, MonthlyPlan $monthlyPlan, $taskId, $fileId)
    {
        if ($monthlyPlan->organization_id !== $request->user()->organization_id) {
            abort(403);
        }

        $task = PlanTask::findOrFail($taskId);
        if ($task->monthly_plan_id !== $monthlyPlan->id) {
            abort(404);
        }

        $file = PlanTaskFile::findOrFail($fileId);
        if ($file->plan_task_id !== $task->id) {
            abort(404);
        }

        if (!Storage::disk('public')->exists($file->file_path)) {
            abort(404, 'الملف غير موجود');
        }

        return Storage::disk('public')->download($file->file_path, $file->file_name);
    }

    /**
     * حفظ تعليق جديد على المهمة
     */
    public function storeComment(Request $request, MonthlyPlan $monthlyPlan, $taskId): RedirectResponse
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

        // التحقق من البيانات
        $request->validate([
            'comment' => 'required|string|max:5000',
        ]);

        // حفظ التعليق
        PlanTaskComment::create([
            'plan_task_id' => $task->id,
            'user_id' => $request->user()->id,
            'comment' => $request->comment,
        ]);

        return redirect()->route('monthly-plans.tasks.show', [$monthlyPlan, $task])
            ->with('success', 'تم إضافة التعليق بنجاح');
    }

    /**
     * تحديث القيمة المحققة للهدف بناءً على المهام المرتبطة به
     */
    private function updateGoalAchievedValue($goalId): void
    {
        $goal = MonthlyPlanGoal::find($goalId);
        if (!$goal) {
            return;
        }

        // حساب عدد المهام المرتبطة بهذا الهدف الموجودة في ready أو publish
        $completedTasksCount = PlanTask::where('goal_id', $goalId)
            ->whereIn('list_type', ['ready', 'publish'])
            ->count();

        // تحديث achieved_value (لا يمكن أن يتجاوز target_value)
        $goal->update(['achieved_value' => min($goal->target_value, $completedTasksCount)]);
    }

    /**
     * إرسال webhook عند إضافة مهمة جديدة لموظف
     */
    private function sendTaskWebhook(PlanTask $task): void
    {
        try {
            $employee = $task->assignedEmployee;
            $project = $task->monthlyPlan->project ?? null;

            // التحقق من وجود بيانات الموظف والمشروع
            if (!$employee || !$project) {
                Log::warning('Cannot send webhook: Missing employee or project data', [
                    'task_id' => $task->id,
                    'employee_id' => $task->assigned_to,
                    'project_id' => $task->monthlyPlan->project_id ?? null,
                ]);
                return;
            }

            // التحقق من وجود رقم الهاتف
            if (!$employee->phone) {
                Log::warning('Cannot send webhook: Employee phone number is missing', [
                    'task_id' => $task->id,
                    'employee_id' => $employee->id,
                ]);
                return;
            }

            // إضافة "2" في بداية رقم الهاتف إذا لم يكن موجوداً
            $phoneNumber = $employee->phone;
         

            $webhookUrl = 'https://n8n.marketlink.app/webhook/ffd53780-5656-450a-b6d1-543b71bd2ae8';

            $response = Http::timeout(10)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post($webhookUrl, [
                    'phoneNumber' => $phoneNumber,
                    'empName' => $employee->name,
                    'TaskTitle' => $task->title,
                    'ProjectName' => $project->business_name ?? 'غير محدد',
                    'TaskID' => $task->id,
                ]);

            if ($response->successful()) {
                Log::info('Webhook sent successfully', [
                    'task_id' => $task->id,
                    'employee_id' => $employee->id,
                    'webhook_url' => $webhookUrl,
                ]);
            } else {
                Log::error('Webhook request failed', [
                    'task_id' => $task->id,
                    'employee_id' => $employee->id,
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            // تسجيل الخطأ ولكن عدم إيقاف عملية إنشاء المهمة
            Log::error('Error sending webhook', [
                'task_id' => $task->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * حذف ملف مرفق بالمهمة
     */
    public function deleteFile(Request $request, MonthlyPlan $monthlyPlan, $taskId, $fileId)
    {
        if ($monthlyPlan->organization_id !== $request->user()->organization_id) {
            if ($request->expectsJson() || $request->header('Content-Type') === 'application/json') {
                return response()->json(['error' => 'غير مصرح'], 403);
            }
            abort(403);
        }

        $task = PlanTask::findOrFail($taskId);
        if ($task->monthly_plan_id !== $monthlyPlan->id) {
            if ($request->expectsJson() || $request->header('Content-Type') === 'application/json') {
                return response()->json(['error' => 'المهمة غير مرتبطة بهذه الخطة'], 404);
            }
            abort(404);
        }

        $file = PlanTaskFile::findOrFail($fileId);
        if ($file->plan_task_id !== $task->id) {
            if ($request->expectsJson() || $request->header('Content-Type') === 'application/json') {
                return response()->json(['error' => 'الملف غير مرتبط بهذه المهمة'], 404);
            }
            abort(404);
        }

        try {
            // حذف الملف من التخزين
            if (Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }

            // حذف السجل من قاعدة البيانات
            $file->delete();

            // للطلبات JSON
            if ($request->expectsJson() || $request->header('Content-Type') === 'application/json') {
                return response()->json([
                    'success' => true,
                ]);
            }

            // Default: redirect
            return redirect()->back()
                ->with('success', 'تم حذف الملف بنجاح');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->header('Content-Type') === 'application/json') {
                return response()->json([
                    'success' => false,
                    'error' => 'حدث خطأ أثناء حذف الملف: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء حذف الملف: ' . $e->getMessage());
        }
    }

    /**
     * Generate description from idea using webhook
     */
    public function generateDescription(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'idea' => 'required|string',
            ]);

            $idea = $request->input('idea');

            // Call the webhook
            $response = Http::timeout(120)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post('https://n8n.marketlink.app/webhook/ef5551bb-1201-4b39-8731-8976da6ea273', [
                    'idea' => $idea
                ]);

            if (!$response->successful()) {
                Log::error('Webhook error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                
                return response()->json([
                    'success' => false,
                    'error' => 'فشل في إنشاء الوصف. يرجى المحاولة مرة أخرى.',
                ], $response->status());
            }

            // Get response body as text first
            $responseBody = $response->body();
            Log::info('Webhook response', [
                'status' => $response->status(),
                'content_type' => $response->header('Content-Type'),
                'body_length' => strlen($responseBody),
            ]);
            
            // Try to parse as JSON
            $data = null;
            $contentType = $response->header('Content-Type', '');
            
            if (str_contains($contentType, 'application/json')) {
                $data = $response->json();
            } else {
                // Try to parse as JSON anyway
                $jsonData = json_decode($responseBody, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $data = $jsonData;
                } else {
                    // If not JSON, treat as plain text
                    $data = $responseBody;
                }
            }
            
            // Extract description from response
            $description = '';
            if (is_string($data)) {
                // If it's a string, use it directly
                $description = $data;
            } elseif (is_array($data)) {
                // Try common field names
                if (isset($data['description'])) {
                    $description = $data['description'];
                } elseif (isset($data['content'])) {
                    $description = $data['content'];
                } elseif (isset($data['text'])) {
                    $description = $data['text'];
                } elseif (isset($data['result'])) {
                    $description = $data['result'];
                } elseif (isset($data['output'])) {
                    $description = $data['output'];
                } elseif (isset($data['message'])) {
                    $description = $data['message'];
                } elseif (isset($data['body'])) {
                    $description = $data['body'];
                } elseif (is_array($data) && count($data) > 0) {
                    // If it's an array, try to get first item
                    $firstItem = reset($data);
                    $description = is_string($firstItem) ? $firstItem : json_encode($firstItem, JSON_UNESCAPED_UNICODE);
                } else {
                    // Last resort: stringify the whole object
                    $description = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                }
            }

            if (empty($description)) {
                return response()->json([
                    'success' => false,
                    'error' => 'لم يتم إرجاع وصف من الخادم',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'description' => $description,
            ]);

        } catch (\Exception $e) {
            Log::error('Error generating description', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'حدث خطأ أثناء إنشاء الوصف: ' . $e->getMessage(),
            ], 500);
        }
    }
}
