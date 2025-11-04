<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\MonthlyPlan;
use App\Models\PlanTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeMonthlyPlanController extends Controller
{
    /**
     * Display a listing of monthly plans for projects where employee is manager.
     */
    public function index()
    {
        $employee = Auth::guard('employee')->user();
        
        // جلب المشاريع التي الموظف مدير عليها
        $managedProjectIds = $employee->managedProjects()->pluck('projects.id')->toArray();
        
        // جلب الخطط الشهرية للمشاريع التي الموظف مدير عليها
        $monthlyPlans = MonthlyPlan::whereIn('project_id', $managedProjectIds)
            ->with(['project.client', 'goals', 'employees'])
            ->orderBy('year', 'desc')
            ->orderBy('month_number', 'desc')
            ->get();

        // إحصائيات
        $stats = [
            'total' => $monthlyPlans->count(),
            'active' => $monthlyPlans->where('status', 'active')->count(),
            'completed' => $monthlyPlans->where('status', 'completed')->count(),
            'draft' => $monthlyPlans->where('status', 'draft')->count(),
        ];

        return view('employee.monthly-plans.index', compact('monthlyPlans', 'stats'));
    }

    /**
     * Display the specified monthly plan.
     */
    public function show($monthlyPlanId)
    {
        $employee = Auth::guard('employee')->user();
        
        // التحقق من أن الموظف مدير على المشروع
        $monthlyPlan = MonthlyPlan::with(['project.client', 'goals', 'employees'])
            ->findOrFail($monthlyPlanId);

        if (!$employee->isManagerOfProject($monthlyPlan->project_id)) {
            abort(403, 'ليس لديك صلاحية للوصول إلى هذه الخطة');
        }

        // جلب جميع المهام في هذه الخطة
        $tasks = $monthlyPlan->tasks()
            ->with(['assignedEmployee', 'goal'])
            ->orderBy('order')
            ->get();

        // تجميع المهام حسب القائمة
        $tasksByList = collect();
        
        // مهام عامة
        $tasksByList->put('tasks', $tasks->where('list_type', 'tasks')->whereNull('assigned_to'));
        
        // مهام الموظفين
        foreach ($monthlyPlan->employees as $emp) {
            $tasksByList->put('employee_' . $emp->id, $tasks->where('list_type', 'employee')->where('assigned_to', $emp->id));
        }
        
        // Ready
        $tasksByList->put('ready', $tasks->where('list_type', 'ready'));
        
        // Publish
        $tasksByList->put('publish', $tasks->where('list_type', 'publish'));

        $isManager = true; // الموظف مدير على هذا المشروع

        return view('employee.monthly-plans.show', compact('monthlyPlan', 'tasksByList', 'tasks', 'isManager'));
    }
}
