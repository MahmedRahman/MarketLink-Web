<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\PlanTask;
use Illuminate\Http\Request;

class TasksController extends Controller
{
    /**
     * الحصول على المهام أو تفاصيل مهمة معينة بناءً على رقم التليفون
     * إذا تم تمرير title في query parameters يرجع تفاصيل المهمة
     * إذا لم يتم تمرير title يرجع قائمة المهام
     */
    public function getTasks(Request $request)
    {
        // التحقق من وجود رقم التليفون
        $phone = $request->query('phone');
        $taskTitle = $request->query('title');
        
        if (!$phone) {
            return response()->json([
                'success' => false,
                'message' => 'رقم التليفون مطلوب'
            ], 400);
        }

        // البحث عن الموظف بناءً على رقم التليفون
        $employee = Employee::where('phone', $phone)->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم العثور على موظف بهذا رقم التليفون'
            ], 404);
        }

        // إذا تم تمرير title، إرجاع تفاصيل المهمة
        if ($taskTitle) {
            // جلب المهمة مع جميع العلاقات بناءً على العنوان
            $task = PlanTask::where('title', $taskTitle)
                ->where('assigned_to', $employee->id)
                ->with([
                    'monthlyPlan.project',
                    'goal',
                    'assignedEmployee',
                    'files',
                    'comments.user'
                ])
                ->first();

            if (!$task) {
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم العثور على المهمة أو لا توجد صلاحية للوصول إليها'
                ], 404);
            }

            // إعداد البيانات
            $data = [
                'id' => $task->id,
                'title' => $task->title,
                'task_name' => $task->title,
                'phone' => $phone,
                'description' => $task->description,
                'status' => $task->status,
                'status_badge' => $task->status_badge,
                'due_date' => $task->due_date ? $task->due_date->format('Y-m-d') : null,
                'color' => $task->color,
                'created_at' => $task->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $task->updated_at->format('Y-m-d H:i:s'),
                'company' => [
                    'name' => $task->monthlyPlan->project->business_name ?? 'غير محدد',
                    'project_id' => $task->monthlyPlan->project->id ?? null,
                ],
                'goal' => $task->goal ? [
                    'id' => $task->goal->id,
                    'name' => $task->goal->goal_name,
                ] : null,
                'assigned_employee' => $task->assignedEmployee ? [
                    'id' => $task->assignedEmployee->id,
                    'name' => $task->assignedEmployee->name,
                    'phone' => $task->assignedEmployee->phone,
                ] : null,
                'files' => $task->files->map(function ($file) {
                    return [
                        'id' => $file->id,
                        'file_name' => $file->file_name,
                        'file_type' => $file->file_type,
                        'file_size' => $file->file_size,
                        'file_url' => $file->file_url,
                        'description' => $file->description,
                        'created_at' => $file->created_at->format('Y-m-d H:i:s'),
                    ];
                }),
                'comments' => $task->comments->map(function ($comment) {
                    return [
                        'id' => $comment->id,
                        'comment' => $comment->comment,
                        'user' => $comment->user ? [
                            'id' => $comment->user->id,
                            'name' => $comment->user->name,
                        ] : null,
                        'created_at' => $comment->created_at->format('Y-m-d H:i:s'),
                    ];
                }),
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);
        }

        // إذا لم يتم تمرير task_id، إرجاع قائمة المهام
        $tasks = PlanTask::where('assigned_to', $employee->id)
            ->with(['monthlyPlan.project'])
            ->get()
            ->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'company_name' => $task->monthlyPlan->project->business_name ?? 'غير محدد',
                    'status' => $task->status,
                    'due_date' => $task->due_date ? $task->due_date->format('Y-m-d') : null,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $tasks
        ], 200);
    }

    /**
     * تحديث حالة المهمة بناءً على رقم التليفون ورقم المهمة
     */
    public function updateTaskStatus(Request $request)
    {
        // التحقق من وجود رقم التليفون
        $phone = $request->query('phone');
        $taskId = $request->query('task_id');
        $status = $request->query('status');
        
        if (!$phone) {
            return response()->json([
                'success' => false,
                'message' => 'رقم التليفون مطلوب'
            ], 400);
        }

        if (!$taskId) {
            return response()->json([
                'success' => false,
                'message' => 'رقم المهمة مطلوب'
            ], 400);
        }

        if (!$status) {
            return response()->json([
                'success' => false,
                'message' => 'الحالة مطلوبة'
            ], 400);
        }

        // التحقق من صحة الحالة
        $allowedStatuses = ['todo', 'in_progress', 'review', 'done', 'publish', 'archived'];
        if (!in_array($status, $allowedStatuses)) {
            return response()->json([
                'success' => false,
                'message' => 'الحالة غير صحيحة. الحالات المتاحة: ' . implode(', ', $allowedStatuses)
            ], 400);
        }

        // تطبيع رقم الهاتف (إزالة المسافات والرموز)
        $normalizedPhone = preg_replace('/[^0-9]/', '', $phone);
        
        // إزالة بادئة 2 إذا كانت موجودة للحصول على الرقم الأساسي
        $basePhone = ltrim($normalizedPhone, '2');
        
        // تحويل task_id إلى integer أولاً للبحث عن المهمة
        $taskIdInt = (int) $taskId;
        
        // البحث عن المهمة أولاً لمعرفة الموظف المخصص لها
        $task = PlanTask::with('monthlyPlan.project')->find($taskIdInt);
        
        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم العثور على المهمة',
                'debug' => [
                    'task_id' => $taskIdInt
                ]
            ], 404);
        }
        
        // إذا كانت المهمة مخصصة لموظف، نبحث عن هذا الموظف تحديداً
        $employee = null;
        if ($task->assigned_to) {
            $assignedEmployee = Employee::find($task->assigned_to);
            if ($assignedEmployee) {
                // التحقق من أن رقم الهاتف يطابق
                $assignedPhoneNormalized = preg_replace('/[^0-9]/', '', $assignedEmployee->phone ?? '');
                $assignedBasePhone = ltrim($assignedPhoneNormalized, '2');
                
                if ($assignedPhoneNormalized === $normalizedPhone || 
                    $assignedBasePhone === $basePhone ||
                    $assignedPhoneNormalized === $basePhone ||
                    $assignedBasePhone === $normalizedPhone ||
                    $assignedEmployee->phone === $phone) {
                    $employee = $assignedEmployee;
                }
            }
        }
        
        // إذا لم نجد الموظف المخصص، نبحث عن أي موظف برقم الهاتف
        if (!$employee) {
            $employee = Employee::where(function($query) use ($phone, $normalizedPhone, $basePhone) {
                $query->where('phone', $phone)
                      ->orWhere('phone', $normalizedPhone)
                      ->orWhere('phone', '2' . $basePhone)
                      ->orWhere('phone', $basePhone)
                      ->orWhereRaw("REPLACE(REPLACE(phone, ' ', ''), '-', '') = ?", [$normalizedPhone])
                      ->orWhereRaw("REPLACE(REPLACE(phone, ' ', ''), '-', '') = ?", ['2' . $basePhone])
                      ->orWhereRaw("REPLACE(REPLACE(phone, ' ', ''), '-', '') = ?", [$basePhone]);
            })->first();
        }

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم العثور على موظف بهذا رقم التليفون',
                'debug' => [
                    'phone_received' => $phone,
                    'normalized_phone' => $normalizedPhone
                ]
            ], 404);
        }

        // التحقق من أن المهمة مخصصة لهذا الموظف أو في قائمة عامة
        $hasAccess = false;
        
        // إذا كانت المهمة مخصصة لهذا الموظف
        if ($task->assigned_to == $employee->id) {
            $hasAccess = true;
        }
        // إذا كانت المهمة في قائمة عامة (list_type = 'tasks' و assigned_to = null)
        elseif ($task->list_type === 'tasks' && $task->assigned_to === null) {
            $hasAccess = true;
        }
        // إذا كان الموظف مدير على المشروع
        elseif ($task->monthlyPlan && $task->monthlyPlan->project_id) {
            $employee->load('projects');
            $hasAccess = $employee->isManagerOfProject($task->monthlyPlan->project_id);
        }
        
        if (!$hasAccess) {
            // جلب معلومات الموظف المخصص للمهمة
            $assignedEmployee = $task->assigned_to ? \App\Models\Employee::find($task->assigned_to) : null;
            
            return response()->json([
                'success' => false,
                'message' => 'لا توجد صلاحية للوصول إلى هذه المهمة. المهمة مخصصة لموظف آخر',
                'debug' => [
                    'task_id' => $taskId,
                    'task_title' => $task->title,
                    'task_list_type' => $task->list_type,
                    'task_assigned_to' => $task->assigned_to,
                    'task_assigned_employee_name' => $assignedEmployee ? $assignedEmployee->name : 'غير مخصصة',
                    'task_assigned_employee_phone' => $assignedEmployee ? $assignedEmployee->phone : 'غير مخصصة',
                    'employee_id' => $employee->id,
                    'employee_name' => $employee->name,
                    'employee_phone' => $employee->phone,
                    'phone_received' => $phone,
                    'normalized_phone' => $normalizedPhone,
                    'base_phone' => $basePhone ?? null
                ]
            ], 403);
        }

        // تحديث حالة المهمة
        $task->update([
            'status' => $status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة المهمة بنجاح',
            'data' => [
                'id' => $task->id,
                'title' => $task->title,
                'status' => $task->status,
                'status_badge' => $task->status_badge,
            ]
        ], 200);
    }

    /**
     * الحصول على جميع الموظفين مع أرقام التليفون والمهام المرتبطة بهم
     * لكل مهمة: العنوان، المشروع التابع له، والحالة
     * الموظفون الذين لا يملكون مهام لن يظهروا في النتيجة
     * المهام بحالة "review" و "archived" لن تظهر
     */
    public function getEmployeesWithTasks(Request $request)
    {
        // جلب جميع الموظفين مع مهامهم (استثناء المهام بحالة review و archived)
        $employees = Employee::with([
            'tasks' => function($query) {
                $query->with(['monthlyPlan.project'])
                    ->whereNotIn('status', ['review', 'archived'])
                    ->orderBy('created_at', 'desc');
            }
        ])
        ->whereNotNull('phone')
        ->has('tasks') // فقط الموظفين الذين لديهم مهام
        ->get()
        ->map(function ($employee) {
            // تصفية المهام مرة أخرى للتأكد (في حالة وجود مهام محملة مسبقاً)
            $filteredTasks = $employee->tasks->filter(function ($task) {
                return !in_array($task->status, ['review', 'archived']);
            });

            return [
                'id' => $employee->id,
                'name' => $employee->name,
                'phone' => $employee->phone,
                'tasks' => $filteredTasks->map(function ($task) {
                    return [
                        'id' => $task->id,
                        'title' => $task->title,
                        'project_name' => $task->monthlyPlan->project->business_name ?? 'غير محدد',
                        'status' => $task->status,
                        'status_badge' => $task->status_badge,
                    ];
                })
            ];
        })
        ->filter(function ($employee) {
            // إزالة الموظفين الذين لا يملكون مهام بعد التصفية
            return $employee['tasks']->count() > 0;
        })
        ->values(); // إعادة ترقيم المصفوفة بعد التصفية

        return response()->json([
            'success' => true,
            'data' => $employees
        ], 200);
    }
}
