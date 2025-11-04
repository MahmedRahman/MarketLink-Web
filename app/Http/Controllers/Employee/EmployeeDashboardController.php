<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\PlanTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeDashboardController extends Controller
{
    /**
     * Display the employee dashboard with assigned tasks.
     */
    public function index()
    {
        $employee = Auth::guard('employee')->user();
        
        // جلب المشاريع التي الموظف مدير عليها
        $managedProjectIds = $employee->managedProjects()->pluck('projects.id')->toArray();
        
        // جلب المهام المخصصة للموظف
        $assignedTasks = PlanTask::where('assigned_to', $employee->id)
            ->with(['monthlyPlan.project', 'goal'])
            ->orderBy('created_at', 'desc')
            ->get();

        // إذا كان الموظف مدير على أي مشروع، جلب جميع المهام في الخطط الشهرية لهذه المشاريع
        $allTasks = $assignedTasks;
        if (count($managedProjectIds) > 0) {
            $managedTasks = PlanTask::whereHas('monthlyPlan', function($query) use ($managedProjectIds) {
                    $query->whereIn('project_id', $managedProjectIds);
                })
                ->with(['monthlyPlan.project', 'goal', 'assignedEmployee'])
                ->orderBy('created_at', 'desc')
                ->get();
            
            // دمج المهام مع تجنب التكرار
            $allTasks = $assignedTasks->merge($managedTasks)->unique('id');
        }

        // تجميع المهام حسب الحالة
        $tasksByStatus = [
            'todo' => $allTasks->where('status', 'todo'),
            'in_progress' => $allTasks->where('status', 'in_progress'),
            'review' => $allTasks->where('status', 'review'),
            'done' => $allTasks->where('status', 'done'),
        ];

        // إحصائيات
        $stats = [
            'total' => $allTasks->count(),
            'todo' => $allTasks->where('status', 'todo')->count(),
            'in_progress' => $allTasks->where('status', 'in_progress')->count(),
            'review' => $allTasks->where('status', 'review')->count(),
            'done' => $allTasks->where('status', 'done')->count(),
            'assigned_only' => $assignedTasks->count(),
            'managed_projects_count' => count($managedProjectIds),
        ];

        $hasManagerRole = count($managedProjectIds) > 0;

        return view('employee.dashboard', compact('allTasks', 'tasksByStatus', 'stats', 'employee', 'hasManagerRole', 'managedProjectIds'));
    }
}
