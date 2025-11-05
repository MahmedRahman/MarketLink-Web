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
}
