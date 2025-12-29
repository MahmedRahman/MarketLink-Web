<?php

namespace App\Http\Controllers;

use App\Models\PlanTask;
use App\Models\PlanTaskFile;
use App\Models\PlanTaskComment;
use App\Models\MonthlyPlan;
use App\Models\MonthlyPlanGoal;
use App\Models\Employee;
use App\Models\BrandStyleExtractor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
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
            'publish_date' => 'nullable|date',
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
            'publish_date' => $request->publish_date,
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
                'publish_date' => 'nullable|date',
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
            
            // حفظ توصيات الفكرة في task_data
            if ($request->has('idea_recommendations')) {
                $ideaRecommendationsValue = $request->idea_recommendations;
                
                Log::info('Saving idea_recommendations', [
                    'has_field' => $request->has('idea_recommendations'),
                    'value' => $ideaRecommendationsValue,
                    'value_length' => strlen($ideaRecommendationsValue ?? ''),
                    'is_empty' => empty(trim($ideaRecommendationsValue ?? ''))
                ]);
                
                if (!empty(trim($ideaRecommendationsValue))) {
                    // تنظيف من HTML tags إذا كانت موجودة
                    $ideaRecommendationsValue = strip_tags($ideaRecommendationsValue);
                    $ideaRecommendationsValue = trim($ideaRecommendationsValue);
                    $taskData['idea_recommendations'] = $ideaRecommendationsValue;
                    
                    Log::info('idea_recommendations saved to task_data', [
                        'cleaned_value' => $ideaRecommendationsValue,
                        'task_data' => $taskData
                    ]);
                } else {
                    unset($taskData['idea_recommendations']);
                    Log::info('idea_recommendations removed from task_data (empty)');
                }
            } else {
                Log::info('idea_recommendations not found in request', [
                    'request_keys' => array_keys($request->all())
                ]);
            }
            
            // حفظ توصيات البوست في task_data
            if ($request->has('post_recommendations')) {
                $postRecommendationsValue = $request->post_recommendations;
                Log::info('Saving post_recommendations', [
                    'has_field' => $request->has('post_recommendations'),
                    'value' => $postRecommendationsValue,
                    'value_length' => strlen($postRecommendationsValue ?? ''),
                    'is_empty' => empty(trim($postRecommendationsValue ?? ''))
                ]);
                
                if (!empty(trim($postRecommendationsValue))) {
                    $taskData['post_recommendations'] = trim($postRecommendationsValue);
                    Log::info('post_recommendations saved to task_data', [
                        'cleaned_value' => trim($postRecommendationsValue),
                        'task_data' => $taskData
                    ]);
                } else {
                    unset($taskData['post_recommendations']);
                    Log::info('post_recommendations removed from task_data (empty)');
                }
            } else {
                Log::info('post_recommendations not found in request', [
                    'request_keys' => array_keys($request->all())
                ]);
            }
            
            // حفظ توصيات التصميم في task_data
            if ($request->has('design_recommendations')) {
                $designRecommendationsValue = $request->design_recommendations;
                if (!empty(trim($designRecommendationsValue))) {
                    $taskData['design_recommendations'] = trim($designRecommendationsValue);
                } else {
                    unset($taskData['design_recommendations']);
                }
            }
            
            // حفظ خيارات البوست في task_data
            if ($request->has('post_platform')) {
                $taskData['post_platform'] = $request->post_platform;
            }
            if ($request->has('post_type')) {
                $taskData['post_type'] = $request->post_type;
            }
            if ($request->has('post_word_count')) {
                $taskData['post_word_count'] = $request->post_word_count;
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
                    Log::info('Saving description in partial update', [
                        'has_field' => $request->has('description'),
                        'value_length' => strlen($request->description ?? ''),
                        'value_preview' => substr($request->description ?? '', 0, 100)
                    ]);
                    $updateData['description'] = $request->description;
                }
                if ($request->has('idea')) {
                    $updateData['idea'] = $request->idea;
                }
                if ($request->has('post')) {
                    $updateData['post'] = $request->post;
                }
                if ($request->has('design')) {
                    $updateData['design'] = $request->design;
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
                if ($request->has('publish_date')) {
                    $updateData['publish_date'] = $request->publish_date ?: null;
                }
                if ($request->has('color')) {
                    $updateData['color'] = $request->color;
                }
                if ($request->has('goal_id')) {
                    $updateData['goal_id'] = $request->goal_id && $request->goal_id !== '' ? $request->goal_id : null;
                }
                // حفظ task_data دائماً إذا كان هناك أي تغييرات في البيانات المرتبطة
                // أو إذا كان taskData يحتوي على بيانات (حتى لو كانت موجودة مسبقاً)
                if ($request->has('links') || $request->has('idea_recommendations') || $request->has('post_recommendations') || $request->has('design_recommendations') || $request->has('post_platform') || $request->has('post_type') || $request->has('post_word_count') || !empty($taskData)) {
                    $updateData['task_data'] = !empty($taskData) ? $taskData : null;
                }
                $task->update($updateData);
            } else {
                // تحديث كامل - نستخدم القيم المحسوبة
                Log::info('Full update - saving description and task_data', [
                    'description_length' => strlen($request->description ?? ''),
                    'description_preview' => substr($request->description ?? '', 0, 100),
                    'task_data' => $taskData,
                    'has_post_recommendations' => isset($taskData['post_recommendations'])
                ]);
                
                $updateData = [
                    'title' => $request->title,
                    'description' => $request->description,
                    'idea' => $request->idea,
                    'post' => $request->post ?? null,
                    'design' => $request->design ?? null,
                    'assigned_to' => $newAssignedTo,
                    'due_date' => $request->due_date ?: null,
                    'publish_date' => $request->publish_date ?: null,
                    'status' => $request->status,
                    'list_type' => $newListType,
                    'color' => $request->color ?? '#6366f1',
                    'goal_id' => $request->goal_id && $request->goal_id !== '' ? $request->goal_id : null,
                    'task_data' => !empty($taskData) ? $taskData : null,
                ];
                $task->update($updateData);
                
                Log::info('Update completed', [
                    'task_id' => $task->id,
                    'description_saved' => strlen($task->description ?? ''),
                    'task_data_saved' => $task->task_data
                ]);
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

    /**
     * Bulk assign tasks to employee
     */
    public function bulkAssign(Request $request, MonthlyPlan $monthlyPlan): JsonResponse
    {
        if ($monthlyPlan->organization_id !== $request->user()->organization_id) {
            return response()->json(['error' => 'غير مصرح'], 403);
        }

        $request->validate([
            'task_ids' => 'required|array|min:1',
            'task_ids.*' => 'required|integer|exists:plan_tasks,id',
            'assigned_to' => 'nullable|exists:employees,id',
            'list_type' => 'required|in:tasks,employee,ready,publish',
        ]);

        try {
            $taskIds = $request->task_ids;
            $assignedTo = $request->assigned_to;
            $listType = $request->list_type;

            // التحقق من أن جميع المهام تنتمي لهذه الخطة
            $tasks = PlanTask::whereIn('id', $taskIds)
                ->where('monthly_plan_id', $monthlyPlan->id)
                ->get();

            if ($tasks->count() !== count($taskIds)) {
                return response()->json([
                    'success' => false,
                    'error' => 'بعض المهام المحددة غير موجودة أو لا تنتمي لهذه الخطة'
                ], 400);
            }

            // التحقق من أن الموظف ينتمي للمنظمة إذا تم تحديده
            if ($assignedTo) {
                $employee = Employee::where('id', $assignedTo)
                    ->where('organization_id', $monthlyPlan->organization_id)
                    ->first();

                if (!$employee) {
                    return response()->json([
                        'success' => false,
                        'error' => 'الموظف المحدد غير موجود'
                    ], 400);
                }
            }

            // تحديث جميع المهام
            $updatedCount = 0;
            foreach ($tasks as $task) {
                $oldListType = $task->list_type;
                $oldAssignedTo = $task->assigned_to;

                $task->update([
                    'assigned_to' => $assignedTo,
                    'list_type' => $listType,
                ]);

                // إرسال webhook إذا تم تعيين المهمة لموظف
                if ($assignedTo && $task->assignedEmployee) {
                    $this->sendTaskWebhook($task);
                }

                // تحديث achieved_value إذا كانت المهمة مرتبطة بهدف
                if ($task->goal_id && in_array($listType, ['ready', 'publish'])) {
                    $this->updateGoalAchievedValue($task->goal_id);
                }

                $updatedCount++;
            }

            return response()->json([
                'success' => true,
                'message' => "تم تعيين {$updatedCount} مهمة بنجاح",
                'updated_count' => $updatedCount
            ]);

        } catch (\Exception $e) {
            Log::error('Error in bulk assign: ' . $e->getMessage(), [
                'task_ids' => $request->task_ids ?? [],
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'حدث خطأ أثناء تعيين المهام: ' . $e->getMessage(),
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

    /**
     * Suggest ideas for a task using DeepSeek API
     */
    public function suggestIdeas(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'task_id' => 'required|integer|exists:plan_tasks,id',
                'title' => 'nullable|string',
                'description' => 'nullable|string',
                'idea_recommendations' => 'nullable|string',
            ]);

            $taskId = $request->input('task_id');
            $title = $request->input('title', '');
            $description = $request->input('description', '');
            $ideaRecommendationsFromRequest = $request->input('idea_recommendations', '');

            // جلب المهمة والمشروع
            $task = PlanTask::with(['monthlyPlan.project'])->findOrFail($taskId);
            $project = $task->monthlyPlan->project ?? null;

            if (!$project) {
                return response()->json([
                    'success' => false,
                    'error' => 'المشروع غير موجود'
                ], 400);
            }

            $apiKey = config('services.deepseek.api_key');
            
            if (!$apiKey) {
                return response()->json([
                    'success' => false,
                    'error' => 'DeepSeek API key غير موجود'
                ], 400);
            }

            // جلب البوستات من المشروع
            $textContentTypes = ['text', 'post', 'reels', 'book'];
            $posts = BrandStyleExtractor::where('project_id', $project->id)
                ->whereIn('content_type', $textContentTypes)
                ->whereNotNull('content')
                ->where('content', '!=', '')
                ->orderBy('created_at', 'desc')
                ->limit(10) // أخذ آخر 10 بوستات كمثال
                ->get();

            // بناء أمثلة البوستات
            $postsExamples = '';
            if ($posts->count() > 0) {
                $postsExamples = "\n\nأمثلة على البوستات الموجودة في المشروع:\n";
                foreach ($posts as $index => $post) {
                    $postsExamples .= "\nمثال " . ($index + 1) . ":\n";
                    $postsExamples .= "نوع المحتوى: {$post->content_type_label}\n";
                    $postsExamples .= "المحتوى: " . Str::limit($post->content, 300) . "\n";
                }
            }

            // بناء Brand Profile
            $brandProfileText = '';
            if ($project->brand_profile && is_array($project->brand_profile) && count($project->brand_profile) > 0) {
                $brandProfileText = "\n\nBrand Profile الخاص بالمشروع:\n";
                foreach ($project->brand_profile as $key => $value) {
                    if (!empty($value)) {
                        $keyLabel = match($key) {
                            'voice' => 'Voice',
                            'tone' => 'Tone',
                            'structure' => 'Structure',
                            'language_style' => 'Language Style',
                            'cta_style' => 'CTA Style',
                            'enemy' => 'Enemy',
                            'values' => 'Values',
                            'hook_patterns' => 'Hook Patterns',
                            'phrases' => 'Phrases',
                            default => $key
                        };
                        $brandProfileText .= "- {$keyLabel}: {$value}\n";
                    }
                }
            }

            // استخدام التوصيات من الطلب أولاً، وإذا لم تكن موجودة، جلبها من task_data
            $ideaRecommendations = '';
            
            if (!empty(trim($ideaRecommendationsFromRequest))) {
                // استخدام التوصيات من الطلب مباشرة
                $ideaRecommendations = strip_tags($ideaRecommendationsFromRequest);
                $ideaRecommendations = trim($ideaRecommendations);
            } else {
                // جلب توصيات الفكرة من task_data - مع إعادة تحميل المهمة للتأكد من أحدث البيانات
                $task->refresh();
                $taskData = $task->task_data ?? [];
                
                if (is_array($taskData) && isset($taskData['idea_recommendations'])) {
                    $ideaRecommendations = $taskData['idea_recommendations'];
                } elseif (is_string($taskData)) {
                    // إذا كان task_data نص JSON، نحاول تحليله
                    $decodedTaskData = json_decode($taskData, true);
                    if (is_array($decodedTaskData) && isset($decodedTaskData['idea_recommendations'])) {
                        $ideaRecommendations = $decodedTaskData['idea_recommendations'];
                    }
                }
                
                // تنظيف التوصيات من أي HTML tags إذا كانت موجودة
                if (!empty($ideaRecommendations)) {
                    $ideaRecommendations = strip_tags($ideaRecommendations);
                    $ideaRecommendations = trim($ideaRecommendations);
                }
            }
            
            Log::info('Idea recommendations check', [
                'task_id' => $taskId,
                'from_request' => !empty(trim($ideaRecommendationsFromRequest)),
                'idea_recommendations' => $ideaRecommendations,
                'has_recommendations' => !empty($ideaRecommendations),
                'recommendations_length' => strlen($ideaRecommendations ?? '')
            ]);

            // بناء الـ prompt
            $context = '';
            if ($title) {
                $context .= "عنوان المهمة: {$title}\n";
            }
            if ($description) {
                $context .= "الوصف الحالي: {$description}\n";
            }

            // بناء البرومبت مع إعطاء توصيات الفكرة أهمية عالية جداً
            $recommendationsSection = '';
            if ($ideaRecommendations && !empty(trim($ideaRecommendations))) {
                $recommendationsSection = "\n\n⚠️⚠️⚠️ توصيات الفكرة (أهمية عالية جداً - يجب الالتزام بها بشكل كامل):\n{$ideaRecommendations}\n\n";
            }

            $prompt = "أنت خبير في اقتراح أفكار للمحتوى. بناءً على المعلومات التالية، اقترح فكرة واحدة جديدة ومبتكرة للمهمة.

{$context}{$recommendationsSection}{$postsExamples}{$brandProfileText}

المتطلبات الأساسية:
1. الفكرة يجب أن تكون جديدة ومبتكرة (ليست موجودة في الأمثلة أعلاه)
2. الفكرة يجب أن تكون من 3 إلى 4 سطور
3. الفكرة يجب أن تكون مكتوبة بأسلوب واضح ومباشر
4. الفكرة يجب أن تتماشى مع Brand Profile الخاص بالمشروع
5. الفكرة يجب أن تكون عملية وقابلة للتنفيذ

⚠️ متطلبات التوصيات (أهمية عالية جداً - إلزامي):
" . ($ideaRecommendations && !empty(trim($ideaRecommendations)) ? "6. يجب أن تكون الفكرة المقترحة متوافقة تماماً مع التوصيات المذكورة أعلاه وتأخذها في الاعتبار بشكل كامل
7. التوصيات المذكورة هي أولوية قصوى ويجب أن تنعكس بشكل واضح في الفكرة المقترحة
8. إذا كانت التوصيات تتطلب تعديلات أو إضافات معينة، يجب أن تكون الفكرة المقترحة تتضمن هذه التعديلات والإضافات بشكل واضح ومباشر" : "6. لا توجد توصيات حالياً") . "

أرجع فقط الفكرة المقترحة بدون أي نص إضافي أو شرح أو عناوين.";

            Log::info('Sending request to DeepSeek API for idea suggestions', [
                'title' => $title,
                'has_description' => !empty($description),
                'has_recommendations' => !empty($ideaRecommendations),
                'recommendations_length' => strlen($ideaRecommendations ?? ''),
                'recommendations_preview' => substr($ideaRecommendations ?? '', 0, 200),
                'prompt_length' => strlen($prompt),
                'prompt_preview' => substr($prompt, 0, 500)
            ]);

            $response = Http::timeout(120)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $apiKey,
                ])
                ->post('https://api.deepseek.com/v1/chat/completions', [
                    'model' => 'deepseek-chat',
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'temperature' => 0.8
                ]);

            if (!$response->successful()) {
                Log::error('DeepSeek API error for idea suggestions', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                
                return response()->json([
                    'success' => false,
                    'error' => 'فشل في اقتراح الأفكار. يرجى المحاولة مرة أخرى.',
                ], $response->status());
            }

            $responseData = $response->json();
            $aiResponse = $responseData['choices'][0]['message']['content'] ?? '';
            
            if (empty($aiResponse)) {
                Log::error('Empty response from DeepSeek for idea suggestions');
                return response()->json([
                    'success' => false,
                    'error' => 'استجابة فارغة من AI',
                ], 500);
            }

            Log::info('AI Response received for idea suggestions', [
                'response_length' => strlen($aiResponse),
                'response_preview' => substr($aiResponse, 0, 200)
            ]);

            // تنظيف الرد
            $aiResponse = trim($aiResponse);
            
            // إزالة أي عناوين أو تنسيقات غير مرغوبة
            $aiResponse = preg_replace('/^(فكرة|الفكرة|الفكرة المقترحة|Idea|Suggested Idea)[:\s]*/i', '', $aiResponse);
            $aiResponse = preg_replace('/^[-•\d\.\s]+/', '', $aiResponse);
            $aiResponse = trim($aiResponse);

            if (empty($aiResponse) || strlen($aiResponse) < 20) {
                return response()->json([
                    'success' => false,
                    'error' => 'لم يتم اقتراح فكرة صحيحة',
                ], 400);
            }

            // استخدام الفكرة المقترحة مباشرة
            $suggestedIdeas = $aiResponse;
            
            // دمج توصيات الفكرة مع الفكرة المقترحة إذا كانت موجودة
            $ideaRecommendations = $task->task_data['idea_recommendations'] ?? '';
            if ($ideaRecommendations && !empty(trim($ideaRecommendations))) {
                $suggestedIdeas = $suggestedIdeas . "\n\n---\n\nتوصيات:\n" . $ideaRecommendations;
            }

            return response()->json([
                'success' => true,
                'ideas' => $suggestedIdeas,
            ]);

        } catch (\Exception $e) {
            Log::error('Error suggesting ideas', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'حدث خطأ أثناء اقتراح الأفكار: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the prompt that would be sent to DeepSeek API
     */
    public function showPrompt(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'task_id' => 'required|integer|exists:plan_tasks,id',
                'title' => 'nullable|string',
                'description' => 'nullable|string',
                'idea_recommendations' => 'nullable|string',
            ]);

            $taskId = $request->input('task_id');
            $title = $request->input('title', '');
            $description = $request->input('description', '');
            $ideaRecommendationsFromRequest = $request->input('idea_recommendations', '');

            // جلب المهمة والمشروع
            $task = PlanTask::with(['monthlyPlan.project'])->findOrFail($taskId);
            $project = $task->monthlyPlan->project ?? null;

            if (!$project) {
                return response()->json([
                    'success' => false,
                    'error' => 'المشروع غير موجود'
                ], 400);
            }

            // جلب البوستات من المشروع
            $textContentTypes = ['text', 'post', 'reels', 'book'];
            $posts = BrandStyleExtractor::where('project_id', $project->id)
                ->whereIn('content_type', $textContentTypes)
                ->whereNotNull('content')
                ->where('content', '!=', '')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            // بناء أمثلة البوستات
            $postsExamples = '';
            if ($posts->count() > 0) {
                $postsExamples = "\n\nأمثلة على البوستات الموجودة في المشروع:\n";
                foreach ($posts as $index => $post) {
                    $postsExamples .= "\nمثال " . ($index + 1) . ":\n";
                    $postsExamples .= "نوع المحتوى: {$post->content_type_label}\n";
                    $postsExamples .= "المحتوى: " . Str::limit($post->content, 300) . "\n";
                }
            }

            // بناء Brand Profile
            $brandProfileText = '';
            if ($project->brand_profile && is_array($project->brand_profile) && count($project->brand_profile) > 0) {
                $brandProfileText = "\n\nBrand Profile الخاص بالمشروع:\n";
                foreach ($project->brand_profile as $key => $value) {
                    if (!empty($value)) {
                        $keyLabel = match($key) {
                            'voice' => 'Voice',
                            'tone' => 'Tone',
                            'structure' => 'Structure',
                            'language_style' => 'Language Style',
                            'cta_style' => 'CTA Style',
                            'enemy' => 'Enemy',
                            'values' => 'Values',
                            'hook_patterns' => 'Hook Patterns',
                            'phrases' => 'Phrases',
                            default => $key
                        };
                        $brandProfileText .= "- {$keyLabel}: {$value}\n";
                    }
                }
            }

            // استخدام التوصيات من الطلب أولاً، وإذا لم تكن موجودة، جلبها من task_data
            $ideaRecommendations = '';
            
            if (!empty(trim($ideaRecommendationsFromRequest))) {
                // استخدام التوصيات من الطلب مباشرة
                $ideaRecommendations = strip_tags($ideaRecommendationsFromRequest);
                $ideaRecommendations = trim($ideaRecommendations);
            } else {
                // جلب توصيات الفكرة من task_data
                $taskData = $task->task_data ?? [];
                
                if (is_array($taskData) && isset($taskData['idea_recommendations'])) {
                    $ideaRecommendations = $taskData['idea_recommendations'];
                } elseif (is_string($taskData)) {
                    $decodedTaskData = json_decode($taskData, true);
                    if (is_array($decodedTaskData) && isset($decodedTaskData['idea_recommendations'])) {
                        $ideaRecommendations = $decodedTaskData['idea_recommendations'];
                    }
                }
                
                if (!empty($ideaRecommendations)) {
                    $ideaRecommendations = strip_tags($ideaRecommendations);
                    $ideaRecommendations = trim($ideaRecommendations);
                }
            }
            
            Log::info('Idea recommendations check (showPrompt)', [
                'task_id' => $taskId,
                'from_request' => !empty(trim($ideaRecommendationsFromRequest)),
                'idea_recommendations' => $ideaRecommendations,
                'has_recommendations' => !empty($ideaRecommendations),
                'recommendations_length' => strlen($ideaRecommendations ?? '')
            ]);

            // بناء الـ prompt
            $context = '';
            if ($title) {
                $context .= "عنوان المهمة: {$title}\n";
            }
            if ($description) {
                $context .= "الوصف الحالي: {$description}\n";
            }

            // بناء البرومبت مع إعطاء توصيات الفكرة أهمية عالية جداً
            $recommendationsSection = '';
            if ($ideaRecommendations && !empty(trim($ideaRecommendations))) {
                $recommendationsSection = "\n\n⚠️⚠️⚠️ توصيات الفكرة (أهمية عالية جداً - يجب الالتزام بها بشكل كامل):\n{$ideaRecommendations}\n\n";
            }

            $prompt = "أنت خبير في اقتراح أفكار للمحتوى. بناءً على المعلومات التالية، اقترح فكرة واحدة جديدة ومبتكرة للمهمة.

{$context}{$recommendationsSection}{$postsExamples}{$brandProfileText}

المتطلبات الأساسية:
1. الفكرة يجب أن تكون جديدة ومبتكرة (ليست موجودة في الأمثلة أعلاه)
2. الفكرة يجب أن تكون من 3 إلى 4 سطور
3. الفكرة يجب أن تكون مكتوبة بأسلوب واضح ومباشر
4. الفكرة يجب أن تتماشى مع Brand Profile الخاص بالمشروع
5. الفكرة يجب أن تكون عملية وقابلة للتنفيذ

⚠️ متطلبات التوصيات (أهمية عالية جداً - إلزامي):
" . ($ideaRecommendations && !empty(trim($ideaRecommendations)) ? "6. يجب أن تكون الفكرة المقترحة متوافقة تماماً مع التوصيات المذكورة أعلاه وتأخذها في الاعتبار بشكل كامل
7. التوصيات المذكورة هي أولوية قصوى ويجب أن تنعكس بشكل واضح في الفكرة المقترحة
8. إذا كانت التوصيات تتطلب تعديلات أو إضافات معينة، يجب أن تكون الفكرة المقترحة تتضمن هذه التعديلات والإضافات بشكل واضح ومباشر" : "6. لا توجد توصيات حالياً") . "

أرجع فقط الفكرة المقترحة بدون أي نص إضافي أو شرح أو عناوين.";

            return response()->json([
                'success' => true,
                'prompt' => $prompt,
                'message' => 'تم إنشاء البرومبت بنجاح'
            ]);

        } catch (\Exception $e) {
            Log::error('Error showing prompt', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'حدث خطأ أثناء إنشاء البرومبت: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Suggest post content using DeepSeek API
     */
    public function suggestPost(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'task_id' => 'required|integer|exists:plan_tasks,id',
                'description' => 'nullable|string',
                'idea' => 'nullable|string',
                'post_recommendations' => 'nullable|string',
                'post_platform' => 'nullable|string|in:facebook,linkedin,tiktok,instagram,twitter',
                'post_type' => 'nullable|string|in:text,video,carousel,reels,story',
                'post_word_count' => 'nullable|string',
            ]);

            $taskId = $request->input('task_id');
            $description = $request->input('description', '');
            $idea = $request->input('idea', '');
            $postRecommendations = $request->input('post_recommendations', '');
            $postPlatform = $request->input('post_platform', '');
            $postType = $request->input('post_type', '');
            $postWordCount = $request->input('post_word_count', '');

            // جلب المهمة والمشروع
            $task = PlanTask::with(['monthlyPlan.project'])->findOrFail($taskId);
            $project = $task->monthlyPlan->project ?? null;

            if (!$project) {
                return response()->json([
                    'success' => false,
                    'error' => 'المشروع غير موجود'
                ], 400);
            }

            $apiKey = config('services.deepseek.api_key');
            
            if (!$apiKey) {
                return response()->json([
                    'success' => false,
                    'error' => 'DeepSeek API key غير موجود'
                ], 400);
            }

            // جلب البوستات من المشروع كأمثلة
            $textContentTypes = ['text', 'post', 'reels', 'book'];
            $posts = BrandStyleExtractor::where('project_id', $project->id)
                ->whereIn('content_type', $textContentTypes)
                ->whereNotNull('content')
                ->where('content', '!=', '')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            // بناء أمثلة البوستات
            $postsExamples = '';
            if ($posts->count() > 0) {
                $postsExamples = "\n\nأمثلة على البوستات الموجودة في المشروع:\n";
                foreach ($posts as $index => $post) {
                    $postsExamples .= "\nمثال " . ($index + 1) . ":\n";
                    $postsExamples .= "نوع المحتوى: {$post->content_type_label}\n";
                    $postsExamples .= "المحتوى: " . Str::limit($post->content, 300) . "\n";
                }
            }

            // بناء Brand Profile
            $brandProfileText = '';
            if ($project->brand_profile && is_array($project->brand_profile) && count($project->brand_profile) > 0) {
                $brandProfileText = "\n\nBrand Profile الخاص بالمشروع:\n";
                foreach ($project->brand_profile as $key => $value) {
                    if (!empty($value)) {
                        $keyLabel = match($key) {
                            'voice' => 'Voice',
                            'tone' => 'Tone',
                            'structure' => 'Structure',
                            'language_style' => 'Language Style',
                            'cta_style' => 'CTA Style',
                            'enemy' => 'Enemy',
                            'values' => 'Values',
                            'hook_patterns' => 'Hook Patterns',
                            'phrases' => 'Phrases',
                            default => $key
                        };
                        $brandProfileText .= "- {$keyLabel}: {$value}\n";
                    }
                }
            }

            // استخدام التوصيات من الطلب أولاً
            $postRecommendationsText = '';
            if (!empty(trim($postRecommendations))) {
                $postRecommendationsText = strip_tags($postRecommendations);
                $postRecommendationsText = trim($postRecommendationsText);
            } else {
                // جلب من task_data إذا لم تكن موجودة في الطلب
                $taskData = $task->task_data ?? [];
                if (is_array($taskData) && isset($taskData['post_recommendations'])) {
                    $postRecommendationsText = strip_tags($taskData['post_recommendations']);
                    $postRecommendationsText = trim($postRecommendationsText);
                }
            }

            // بناء معلومات البوست
            $postInfo = '';
            if ($postPlatform) {
                $platformLabels = [
                    'facebook' => 'فيسبوك',
                    'linkedin' => 'لينكد إن',
                    'tiktok' => 'تيك توك',
                    'instagram' => 'إنستجرام',
                    'twitter' => 'تويتر'
                ];
                $postInfo .= "المنصة: " . ($platformLabels[$postPlatform] ?? $postPlatform) . "\n";
            }
            if ($postType) {
                $typeLabels = [
                    'text' => 'نص',
                    'video' => 'فيديو',
                    'carousel' => 'كروسر',
                    'reels' => 'ريلز',
                    'story' => 'ستوري'
                ];
                $postInfo .= "نوع البوست: " . ($typeLabels[$postType] ?? $postType) . "\n";
            }
            if ($postWordCount) {
                $postInfo .= "عدد الكلمات المطلوب: {$postWordCount} كلمة\n";
            }

            // بناء الـ prompt
            $context = '';
            if ($idea && !empty(trim($idea))) {
                $context .= "الفكرة:\n{$idea}\n\n";
            }
            if ($description && !empty(trim($description))) {
                $context .= "الوصف:\n{$description}\n\n";
            }
            
            $recommendationsSection = '';
            if ($postRecommendationsText && !empty(trim($postRecommendationsText))) {
                $recommendationsSection = "\n\n⚠️⚠️⚠️ توصيات البوست (أهمية عالية جداً - يجب الالتزام بها بشكل كامل):\n{$postRecommendationsText}\n\n";
            }

            $prompt = "أنت خبير في كتابة محتوى البوستات لوسائل التواصل الاجتماعي. بناءً على المعلومات التالية، اكتب بوست كامل وجاهز للنشر.

{$context}{$recommendationsSection}{$postsExamples}{$brandProfileText}

" . ($postInfo ? "معلومات البوست:\n{$postInfo}\n\n" : "") . "المتطلبات:
1. البوست يجب أن يكون جديداً ومبتكراً (ليس نسخة من الأمثلة أعلاه)
2. البوست يجب أن يتماشى تماماً مع Brand Profile الخاص بالمشروع
3. البوست يجب أن يكون جاهزاً للنشر مباشرة
4. استخدم نفس الأسلوب والهيكل الموجود في الأمثلة
5. إذا كانت هناك توصيات للبوست، يجب الالتزام بها بشكل كامل
" . ($postWordCount ? "6. البوست يجب أن يكون حوالي {$postWordCount} كلمة\n" : "") . "
7. البوست يجب أن يكون جذاباً ومقنعاً
8. استخدم نفس النبرة والأسلوب الموجود في الأمثلة

أرجع فقط نص البوست الجاهز للنشر بدون أي نص إضافي أو شرح أو عناوين.";

            Log::info('Sending request to DeepSeek API for post suggestions', [
                'task_id' => $taskId,
                'has_description' => !empty($description),
                'has_idea' => !empty($idea),
                'has_recommendations' => !empty($postRecommendationsText),
                'platform' => $postPlatform,
                'type' => $postType,
                'word_count' => $postWordCount,
                'prompt_length' => strlen($prompt)
            ]);

            // زيادة الـ execution time limit
            set_time_limit(180); // 3 دقائق
            
            $response = Http::timeout(150)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $apiKey,
                ])
                ->post('https://api.deepseek.com/v1/chat/completions', [
                    'model' => 'deepseek-chat',
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'temperature' => 0.8,
                    'max_tokens' => 2000 // تقليل عدد الـ tokens لتسريع الرد
                ]);

            if (!$response->successful()) {
                Log::error('DeepSeek API error for post suggestions', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                
                return response()->json([
                    'success' => false,
                    'error' => 'فشل في اقتراح البوست. يرجى المحاولة مرة أخرى.',
                ], $response->status());
            }

            $responseData = $response->json();
            $aiResponse = $responseData['choices'][0]['message']['content'] ?? '';
            
            if (empty($aiResponse)) {
                Log::error('Empty response from DeepSeek for post suggestions');
                return response()->json([
                    'success' => false,
                    'error' => 'استجابة فارغة من AI',
                ], 500);
            }

            Log::info('AI Response received for post suggestions', [
                'response_length' => strlen($aiResponse),
                'response_preview' => substr($aiResponse, 0, 200)
            ]);

            // تنظيف الرد
            $aiResponse = trim($aiResponse);
            
            // إزالة أي عناوين أو تنسيقات غير مرغوبة
            $aiResponse = preg_replace('/^(بوست|البوست|Post|المحتوى|Content)[:\s]*/i', '', $aiResponse);
            $aiResponse = preg_replace('/^[-•\d\.\s]+/', '', $aiResponse);
            $aiResponse = trim($aiResponse);

            if (empty($aiResponse) || strlen($aiResponse) < 20) {
                return response()->json([
                    'success' => false,
                    'error' => 'لم يتم اقتراح بوست صحيح',
                ], 400);
            }

            return response()->json([
                'success' => true,
                'post' => $aiResponse,
            ]);

        } catch (\Exception $e) {
            Log::error('Error suggesting post', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'حدث خطأ أثناء اقتراح البوست: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the prompt that would be sent to DeepSeek API for post suggestions
     */
    public function showPostPrompt(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'task_id' => 'required|integer|exists:plan_tasks,id',
                'description' => 'nullable|string',
                'idea' => 'nullable|string',
                'post_recommendations' => 'nullable|string',
                'post_platform' => 'nullable|string|in:facebook,linkedin,tiktok,instagram,twitter',
                'post_type' => 'nullable|string|in:text,video,carousel,reels,story',
                'post_word_count' => 'nullable|string',
            ]);

            $taskId = $request->input('task_id');
            $description = $request->input('description', '');
            $idea = $request->input('idea', '');
            $postRecommendations = $request->input('post_recommendations', '');
            $postPlatform = $request->input('post_platform', '');
            $postType = $request->input('post_type', '');
            $postWordCount = $request->input('post_word_count', '');

            // جلب المهمة والمشروع
            $task = PlanTask::with(['monthlyPlan.project'])->findOrFail($taskId);
            $project = $task->monthlyPlan->project ?? null;

            if (!$project) {
                return response()->json([
                    'success' => false,
                    'error' => 'المشروع غير موجود'
                ], 400);
            }

            // جلب البوستات من المشروع كأمثلة
            $textContentTypes = ['text', 'post', 'reels', 'book'];
            $posts = BrandStyleExtractor::where('project_id', $project->id)
                ->whereIn('content_type', $textContentTypes)
                ->whereNotNull('content')
                ->where('content', '!=', '')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            // بناء أمثلة البوستات
            $postsExamples = '';
            if ($posts->count() > 0) {
                $postsExamples = "\n\nأمثلة على البوستات الموجودة في المشروع:\n";
                foreach ($posts as $index => $post) {
                    $postsExamples .= "\nمثال " . ($index + 1) . ":\n";
                    $postsExamples .= "نوع المحتوى: {$post->content_type_label}\n";
                    $postsExamples .= "المحتوى: " . Str::limit($post->content, 300) . "\n";
                }
            }

            // بناء Brand Profile
            $brandProfileText = '';
            if ($project->brand_profile && is_array($project->brand_profile) && count($project->brand_profile) > 0) {
                $brandProfileText = "\n\nBrand Profile الخاص بالمشروع:\n";
                foreach ($project->brand_profile as $key => $value) {
                    if (!empty($value)) {
                        $keyLabel = match($key) {
                            'voice' => 'Voice',
                            'tone' => 'Tone',
                            'structure' => 'Structure',
                            'language_style' => 'Language Style',
                            'cta_style' => 'CTA Style',
                            'enemy' => 'Enemy',
                            'values' => 'Values',
                            'hook_patterns' => 'Hook Patterns',
                            'phrases' => 'Phrases',
                            default => $key
                        };
                        $brandProfileText .= "- {$keyLabel}: {$value}\n";
                    }
                }
            }

            // استخدام التوصيات من الطلب أولاً
            $postRecommendationsText = '';
            if (!empty(trim($postRecommendations))) {
                $postRecommendationsText = strip_tags($postRecommendations);
                $postRecommendationsText = trim($postRecommendationsText);
            } else {
                // جلب من task_data إذا لم تكن موجودة في الطلب
                $taskData = $task->task_data ?? [];
                if (is_array($taskData) && isset($taskData['post_recommendations'])) {
                    $postRecommendationsText = strip_tags($taskData['post_recommendations']);
                    $postRecommendationsText = trim($postRecommendationsText);
                }
            }

            // بناء معلومات البوست
            $postInfo = '';
            if ($postPlatform) {
                $platformLabels = [
                    'facebook' => 'فيسبوك',
                    'linkedin' => 'لينكد إن',
                    'tiktok' => 'تيك توك',
                    'instagram' => 'إنستجرام',
                    'twitter' => 'تويتر'
                ];
                $postInfo .= "المنصة: " . ($platformLabels[$postPlatform] ?? $postPlatform) . "\n";
            }
            if ($postType) {
                $typeLabels = [
                    'text' => 'نص',
                    'video' => 'فيديو',
                    'carousel' => 'كروسر',
                    'reels' => 'ريلز',
                    'story' => 'ستوري'
                ];
                $postInfo .= "نوع البوست: " . ($typeLabels[$postType] ?? $postType) . "\n";
            }
            if ($postWordCount) {
                $postInfo .= "عدد الكلمات المطلوب: {$postWordCount} كلمة\n";
            }

            // جلب معلومات المشروع
            $projectInfo = '';
            if ($project) {
                $projectInfo = "\n\n=== معلومات المشروع ===\n";
                if ($project->business_name) {
                    $projectInfo .= "اسم المشروع/العلامة التجارية: {$project->business_name}\n";
                }
                if ($project->business_description) {
                    $projectInfo .= "وصف المشروع: {$project->business_description}\n";
                }
            }
            
            // بناء الـ prompt بشكل احترافي
            $context = '';
            if ($idea && !empty(trim($idea))) {
                $context .= "=== الفكرة الأساسية ===\n{$idea}\n\n";
            }
            if ($description && !empty(trim($description))) {
                $context .= "=== وصف المهمة ===\n{$description}\n\n";
            }
            
            $recommendationsSection = '';
            if ($postRecommendationsText && !empty(trim($postRecommendationsText))) {
                $recommendationsSection = "\n\n=== ⚠️ توصيات البوست (أهمية عالية جداً - إلزامي) ===\n{$postRecommendationsText}\n\n";
            }

            $prompt = "أنت كاتب محتوى محترف وخبير في كتابة البوستات لوسائل التواصل الاجتماعي. مهمتك هي كتابة محتوى احترافي وجذاب بناءً على المعلومات التالية.

{$projectInfo}{$context}{$recommendationsSection}

=== أمثلة على البوستات السابقة للمشروع ===
{$postsExamples}

=== Brand Profile وأسلوب الكتابة ===
{$brandProfileText}

" . ($postInfo ? "=== مواصفات البوست المطلوب ===\n{$postInfo}\n" : "") . "
=== المتطلبات والضوابط ===
1. البوست يجب أن يكون جديداً ومبتكراً تماماً (ليس نسخة أو تعديل على الأمثلة أعلاه)
2. البوست يجب أن يتماشى 100% مع Brand Profile وأسلوب الكتابة المحدد أعلاه
3. البوست يجب أن يكون جاهزاً للنشر مباشرة بدون أي تعديلات
4. استخدم نفس الأسلوب والهيكل والنبرة الموجودة في الأمثلة السابقة
5. إذا كانت هناك توصيات محددة للبوست، يجب الالتزام بها بشكل كامل ودقيق
" . ($postWordCount ? "6. البوست يجب أن يكون بالضبط حوالي {$postWordCount} كلمة (هام جداً)\n" : "") . "
7. البوست يجب أن يكون جذاباً ومقنعاً ويحفز التفاعل
8. استخدم نفس النبرة والأسلوب واللغة الموجودة في الأمثلة
9. تأكد من أن المحتوى يتماشى مع هوية المشروع ورسالته
10. استخدم نفس أنواع الجمل والتراكيب الموجودة في الأمثلة

=== مهمتك ===
اكتب بوست كامل وجاهز للنشر يتماشى مع جميع المتطلبات أعلاه. البوست يجب أن يكون احترافياً وجذاباً ومتوافقاً تماماً مع أسلوب المشروع.

=== المخرجات المطلوبة ===
أرجع فقط نص البوست الجاهز للنشر بدون أي نص إضافي أو شرح أو عناوين أو تنسيقات غير ضرورية. فقط نص البوست النهائي.";

            Log::info('Prompt for DeepSeek API post suggestions', [
                'prompt' => $prompt,
                'prompt_length' => strlen($prompt)
            ]);

            return response()->json([
                'success' => true,
                'prompt' => $prompt,
                'message' => 'تم إنشاء البرومبت بنجاح'
            ]);

        } catch (\Exception $e) {
            Log::error('Error showing post prompt', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'حدث خطأ أثناء إنشاء البرومبت: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Suggest design using DeepSeek API
     */
    public function suggestDesign(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'task_id' => 'required|integer|exists:plan_tasks,id',
                'design' => 'nullable|string',
                'design_recommendations' => 'nullable|string',
            ]);

            $taskId = $request->input('task_id');
            $design = $request->input('design', '');
            $designRecommendations = $request->input('design_recommendations', '');

            // جلب المهمة والمشروع
            $task = PlanTask::with(['monthlyPlan.project'])->findOrFail($taskId);
            $project = $task->monthlyPlan->project ?? null;

            if (!$project) {
                return response()->json([
                    'success' => false,
                    'error' => 'المشروع غير موجود'
                ], 400);
            }

            $apiKey = config('services.deepseek.api_key');
            
            if (!$apiKey) {
                return response()->json([
                    'success' => false,
                    'error' => 'DeepSeek API key غير موجود'
                ], 400);
            }

            // بناء البرومبت للتصميم
            $projectInfo = "=== معلومات المشروع ===\n";
            $projectInfo .= "اسم المشروع: {$project->business_name}\n";
            if ($project->description) {
                $projectInfo .= "وصف المشروع: {$project->description}\n";
            }
            $projectInfo .= "\n";

            $context = '';
            if ($design && !empty(trim($design))) {
                $context .= "=== التصميم الحالي ===\n{$design}\n\n";
            }

            $recommendationsSection = '';
            if ($designRecommendations && !empty(trim($designRecommendations))) {
                $recommendationsSection = "\n\n=== ⚠️ توصيات التصميم (أهمية عالية جداً - إلزامي) ===\n{$designRecommendations}\n\n";
            }

            // جلب Brand Profile
            $brandProfileText = '';
            if ($project->brand_profile) {
                $brandProfile = is_array($project->brand_profile) ? $project->brand_profile : json_decode($project->brand_profile, true);
                if (is_array($brandProfile) && !empty($brandProfile)) {
                    $brandProfileText = "=== Brand Profile وأسلوب التصميم ===\n";
                    foreach ($brandProfile as $key => $value) {
                        if (!empty($value)) {
                            $brandProfileText .= "{$key}: {$value}\n";
                        }
                    }
                    $brandProfileText .= "\n";
                }
            }

            $prompt = "أنت مصمم جرافيك محترف وخبير في تصميم المحتوى لوسائل التواصل الاجتماعي. مهمتك هي اقتراح تصميم احترافي بناءً على المعلومات التالية.

{$projectInfo}{$context}{$recommendationsSection}

{$brandProfileText}

=== المتطلبات والضوابط ===
1. التصميم المقترح يجب أن يكون جديداً ومبتكراً تماماً
2. التصميم يجب أن يتماشى 100% مع Brand Profile وأسلوب المشروع
3. إذا كانت هناك توصيات محددة للتصميم، يجب الالتزام بها بشكل كامل ودقيق
4. اقترح تفاصيل التصميم مثل: الألوان، الخطوط، التخطيط، العناصر البصرية، النمط
5. التصميم يجب أن يكون مناسباً لوسائل التواصل الاجتماعي
6. قدم وصفاً واضحاً ومفصلاً للتصميم المقترح

=== مهمتك ===
اقترح تصميم كامل ومفصل يتماشى مع جميع المتطلبات أعلاه. قدم وصفاً شاملاً للتصميم يتضمن جميع التفاصيل المطلوبة.

=== المخرجات المطلوبة ===
أرجع فقط وصف التصميم المقترح بدون أي نص إضافي أو شرح أو عناوين.";

            Log::info('Sending request to DeepSeek API for design suggestions', [
                'task_id' => $taskId,
                'has_design' => !empty($design),
                'has_recommendations' => !empty($designRecommendations),
                'prompt_length' => strlen($prompt)
            ]);

            // زيادة الـ execution time limit
            set_time_limit(180); // 3 دقائق
            
            $response = Http::timeout(150)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $apiKey,
                ])
                ->post('https://api.deepseek.com/v1/chat/completions', [
                    'model' => 'deepseek-chat',
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'temperature' => 0.8,
                    'max_tokens' => 2000 // تقليل عدد الـ tokens لتسريع الرد
                ]);

            if (!$response->successful()) {
                Log::error('DeepSeek API error for design suggestions', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                
                return response()->json([
                    'success' => false,
                    'error' => 'فشل في اقتراح التصميم. يرجى المحاولة مرة أخرى.',
                ], $response->status());
            }

            $responseData = $response->json();
            $aiResponse = $responseData['choices'][0]['message']['content'] ?? '';
            
            if (empty($aiResponse)) {
                Log::error('Empty response from DeepSeek for design suggestions');
                return response()->json([
                    'success' => false,
                    'error' => 'استجابة فارغة من AI',
                ], 500);
            }

            // تنظيف الرد
            $aiResponse = trim($aiResponse);
            $aiResponse = preg_replace('/^(تصميم|التصميم|Design|المحتوى|Content)[:\s]*/i', '', $aiResponse);
            $aiResponse = preg_replace('/^[-•\d\.\s]+/', '', $aiResponse);
            $aiResponse = trim($aiResponse);

            if (empty($aiResponse) || strlen($aiResponse) < 20) {
                return response()->json([
                    'success' => false,
                    'error' => 'لم يتم اقتراح تصميم صحيح',
                ], 400);
            }

            return response()->json([
                'success' => true,
                'design' => $aiResponse,
            ]);

        } catch (\Exception $e) {
            Log::error('Error suggesting design', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'حدث خطأ أثناء اقتراح التصميم: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the prompt that would be sent to DeepSeek API for design suggestions
     */
    public function showDesignPrompt(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'task_id' => 'required|integer|exists:plan_tasks,id',
                'design' => 'nullable|string',
                'design_recommendations' => 'nullable|string',
            ]);

            $taskId = $request->input('task_id');
            $design = $request->input('design', '');
            $designRecommendations = $request->input('design_recommendations', '');

            // جلب المهمة والمشروع
            $task = PlanTask::with(['monthlyPlan.project'])->findOrFail($taskId);
            $project = $task->monthlyPlan->project ?? null;

            if (!$project) {
                return response()->json([
                    'success' => false,
                    'error' => 'المشروع غير موجود'
                ], 400);
            }

            // بناء البرومبت للتصميم
            $projectInfo = "=== معلومات المشروع ===\n";
            $projectInfo .= "اسم المشروع: {$project->business_name}\n";
            if ($project->description) {
                $projectInfo .= "وصف المشروع: {$project->description}\n";
            }
            $projectInfo .= "\n";

            $context = '';
            if ($design && !empty(trim($design))) {
                $context .= "=== التصميم الحالي ===\n{$design}\n\n";
            }

            $recommendationsSection = '';
            if ($designRecommendations && !empty(trim($designRecommendations))) {
                $recommendationsSection = "\n\n=== ⚠️ توصيات التصميم (أهمية عالية جداً - إلزامي) ===\n{$designRecommendations}\n\n";
            }

            // جلب Brand Profile
            $brandProfileText = '';
            if ($project->brand_profile) {
                $brandProfile = is_array($project->brand_profile) ? $project->brand_profile : json_decode($project->brand_profile, true);
                if (is_array($brandProfile) && !empty($brandProfile)) {
                    $brandProfileText = "=== Brand Profile وأسلوب التصميم ===\n";
                    foreach ($brandProfile as $key => $value) {
                        if (!empty($value)) {
                            $brandProfileText .= "{$key}: {$value}\n";
                        }
                    }
                    $brandProfileText .= "\n";
                }
            }

            $prompt = "أنت مصمم جرافيك محترف وخبير في تصميم المحتوى لوسائل التواصل الاجتماعي. مهمتك هي اقتراح تصميم احترافي بناءً على المعلومات التالية.

{$projectInfo}{$context}{$recommendationsSection}

{$brandProfileText}

=== المتطلبات والضوابط ===
1. التصميم المقترح يجب أن يكون جديداً ومبتكراً تماماً
2. التصميم يجب أن يتماشى 100% مع Brand Profile وأسلوب المشروع
3. إذا كانت هناك توصيات محددة للتصميم، يجب الالتزام بها بشكل كامل ودقيق
4. اقترح تفاصيل التصميم مثل: الألوان، الخطوط، التخطيط، العناصر البصرية، النمط
5. التصميم يجب أن يكون مناسباً لوسائل التواصل الاجتماعي
6. قدم وصفاً واضحاً ومفصلاً للتصميم المقترح

=== مهمتك ===
اقترح تصميم كامل ومفصل يتماشى مع جميع المتطلبات أعلاه. قدم وصفاً شاملاً للتصميم يتضمن جميع التفاصيل المطلوبة.

=== المخرجات المطلوبة ===
أرجع فقط وصف التصميم المقترح بدون أي نص إضافي أو شرح أو عناوين.";

            return response()->json([
                'success' => true,
                'prompt' => $prompt,
                'message' => 'تم إنشاء البرومبت بنجاح'
            ]);

        } catch (\Exception $e) {
            Log::error('Error showing design prompt', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'حدث خطأ أثناء إنشاء البرومبت: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate design image using Google AI Sandbox API
     */
    public function generateDesignImage(Request $request): JsonResponse
    {
        try {
            set_time_limit(180); // Increase execution time for API calls
            
            $request->validate([
                'task_id' => 'required|integer|exists:plan_tasks,id',
                'design' => 'required|string',
            ]);

            $taskId = $request->input('task_id');
            $design = $request->input('design', '');

            if (empty(trim($design))) {
                return response()->json([
                    'success' => false,
                    'error' => 'يرجى إدخال محتوى التصميم'
                ], 400);
            }

            // Use exact values from curl command - no config needed
            $projectId = 'f49651bf-fd2d-4ef0-b2e6-98813b75f4a6';
            $bearerToken = 'ya29.a0Aa7pCA861v1m4DB60zfGzizfx5ZpvvMpUdxzOOnovFXZRharBv4biTfF_EMNce6tsKDvrJk4arF9ai1aJqTL164gsND0jpvHGe4ILXHkodsMF1OgMtzftc5PT9eau5pca6_oFcz06u-B3UO8qOSc7VIIxLvDkNnHuMfI6jtx1kHHXEWgmXJY2-OfdflYI-QVru5uuRdG3dR-jxnhczv7DJCd41MceWeNuozHBcmy8mbfpggmgzxRObKLEvluuvALpOlUanenbSrTzjDCDkSdcQ0Czs0PBQgnJwLcB-HJmw4i_MSzL7OPikzwLFEUlVKvPtKSRsZBjllgXnRSFrVLqtDenL-KU23u6BjifQ3-cBMaCgYKAZ8SARISFQHGX2Mi4fk-SgZjanwMUWB35poyYw0370';
            
            // Generate session ID (timestamp in milliseconds) - matching curl format
            $sessionId = ';' . round(microtime(true) * 1000);

            // Use the exact recaptchaToken from the curl example (you may need to generate this dynamically)
            $recaptchaToken = "0cAFcWeA6pu6UCsu4StOnazrKaXFjnMm3p-HkNXgkwRftPl1uYq72gW3Z01RzTeSwPtlDVqRapwtragXOaGtwOFwKgx8rmuhPNXYWtaKiNpd81Bqg82hr9Tl6uoMxMTJY5TotIkl0KUsPGWnbh2Sq3ds2DGypz01xjvOLp9qyW7w1Lg1D5xVr7RfOluA7qCNHyb1P_BRZXN0AW076fHME0gRkgHBHOzAw6rCI2u3WVa1V9B38DtNyiY_RD0B56MkPxt5Lne6POEQxu-Z3abjtREM3oDzw-j5xocRW_8Fk17WLh9DAzzY4I_hxFIS4aJwRjbWhJ2sKrlmHq73xHIZ6yHIcqxFpXgHKQFCczjGqpOdc-Ij8l4DoAqIYc5mXTJFzefyTHoxjZXJ_mz_w_gf46B_xCAxniOMff9_KJU0f3NS-tk5l1jp16gO5tVG3_D1KG3oy3CST8X4J0WP44joMrZIx0_-2vGC89C-dcnJbyIbPJUaLT25YTwq5U-OYjVpQfCrHwiFFd-MnOPvHtZ9iqb6GCXKkQPCD6ErbQ5nZQXRv9lTaGPlIsOIWEiCjOaEF9UOH7hTOlGlg1gXspPFmh3KN2a_D46vz6ndhuGZRdZLIGQuqUPGwruj9ciNr-MDtU461ZlSaZci-6Zgf5nB9qTUu_tpMw3UYtea6m9VbBAOMdy-4riI96Nc0eim1ngsEK0jC3TOvQFhtGio6iaSSYnQW_DGlhasdF5-bqUBBSCy_twimrh20ppHH63DX2wcNtUyLGpwvGoOvKIR5tmob_fLnm4XvkFggdEfo1CR_pHD1hfcYcYv09EZCp2wCONMZUi3tzi5pfcZNW4C7ZTRSurjFB2bxWOH4dWmQS82z1tcyq91d7Mn73JjrH9J6nDTus5vLy9aVUBYaYdBhaBakmcEPkjgkVQ2q7xya-v2rvia0YzbYTjUHkPON8IdXQ54bLPKucOfShUvGofEqHgyvBjIXXrDM1N82W9kdmV-i3nflQTGIT679aEg1xNJvKqjUfjCKDkfG69e8yO6rE9bhnstNC9Axf6JRi90rDRXEH0umCGw6eENLbZX00vCBga0E660M8qEiSOep4F2qEiBtC6onhh9OVRu2to5mMoo_v8rOZIKC2e7IaUSZ638rkVYDY0IU_foM8NHH9Hvd63wNaVBOsisgVHdZ9Th3giaTh1sFEyKnQ_TaAdpM4tFNB-IuECWCY_A2IF1q2mCAcbnf5VD838WJVlOHe3wZt0YFjeoVxmmWaV-asm6hHznob0o8cQKK4BaGpFE9rmWoWeT4HKeeH_mV8SP8p3bBC60sLUtfT3CTzC0tZK0OUQxk6j4I-nSq0g9u3rBuLQJkYt_r51lBrSUkeTVneYToFFSd_L8cGu8KCa6ZJqh9Lt1Go3hcEd-40C23JsY7gzlRJEe9tduFBuyBdwrGppzqnieUGwrfw7RRca-hXDclP_lLQ0HmHHIsxGCZmQGEvES7QsVOH3s3zTko3lca-wpVE4vllfZCplrB3vnz1b3AnPNXjnMUZki0xsdPJT62xx38Uelu-IhSE7ZnnU_kBMFDKyKD1pcUxdWDSkZ7fDzQ";

            // Prepare the request body - matching the exact structure from curl
            $requestBody = [
                'clientContext' => [
                    'recaptchaToken' => $recaptchaToken,
                    'sessionId' => $sessionId
                ],
                'requests' => [
                    [
                        'clientContext' => [
                            'recaptchaToken' => $recaptchaToken,
                            'sessionId' => $sessionId,
                            'projectId' => $projectId,
                            'tool' => 'PINHOLE'
                        ],
                        'seed' => 679341, // Using the same seed from curl example
                        'imageModelName' => 'GEM_PIX_2',
                        'imageAspectRatio' => 'IMAGE_ASPECT_RATIO_PORTRAIT',
                        'prompt' => $design, // Only change: use the design input as prompt
                        'imageInputs' => []
                    ]
                ]
            ];

            // Make API call to Google AI Sandbox - matching curl command exactly
            $response = Http::timeout(150)
                ->withHeaders([
                    'sec-ch-ua-platform' => '"macOS"',
                    'Authorization' => 'Bearer ' . $bearerToken,
                    'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36',
                    'sec-ch-ua' => '"Google Chrome";v="143", "Chromium";v="143", "Not A(Brand";v="24"',
                    'Content-Type' => 'text/plain;charset=UTF-8',
                    'sec-ch-ua-mobile' => '?0',
                    'Accept' => '*/*',
                    'X-Browser-Channel' => 'stable',
                    'X-Browser-Year' => '2025',
                    'X-Browser-Validation' => 'AUXUCdutEJ+6gl6bYtz7E2kgIT4=',
                    'X-Browser-Copyright' => 'Copyright 2025 Google LLC. All Rights reserved.',
                    'X-Client-Data' => 'CJG2yQEIprbJAQipncoBCPT5ygEIlaHLAQiGoM0BCPmdzwEYsZvPAQ==',
                    'Sec-Fetch-Site' => 'cross-site',
                    'Sec-Fetch-Mode' => 'cors',
                    'Sec-Fetch-Dest' => 'empty',
                    'host' => 'aisandbox-pa.googleapis.com',
                ])
                ->withBody(json_encode($requestBody), 'text/plain;charset=UTF-8')
                ->post("https://aisandbox-pa.googleapis.com/v1/projects/{$projectId}/flowMedia:batchGenerateImages");

            if (!$response->successful()) {
                Log::error('Google AI Sandbox API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return response()->json([
                    'success' => false,
                    'error' => 'فشل في إنشاء الصورة: ' . ($response->json()['error']['message'] ?? 'خطأ غير معروف')
                ], 500);
            }

            $responseData = $response->json();

            // Extract the base64 encoded image
            if (isset($responseData['media']) && is_array($responseData['media']) && count($responseData['media']) > 0) {
                $firstMedia = $responseData['media'][0];
                if (isset($firstMedia['image']['generatedImage']['encodedImage'])) {
                    $encodedImage = $firstMedia['image']['generatedImage']['encodedImage'];
                    
                    // Remove data URL prefix if present
                    $encodedImage = preg_replace('/^data:image\/[a-z]+;base64,/', '', $encodedImage);
                    
                    return response()->json([
                        'success' => true,
                        'image' => $encodedImage
                    ]);
                }
            }

            return response()->json([
                'success' => false,
                'error' => 'لم يتم العثور على الصورة في الاستجابة'
            ], 500);

        } catch (\Exception $e) {
            Log::error('Error generating design image: ' . $e->getMessage(), [
                'task_id' => $request->input('task_id'),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'حدث خطأ أثناء إنشاء الصورة: ' . $e->getMessage(),
            ], 500);
        }
    }
}
