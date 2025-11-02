<?php

namespace App\Http\Controllers;

use App\Models\MonthlyPlan;
use App\Models\Project;
use App\Models\Employee;
use App\Models\MonthlyPlanGoal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MonthlyPlanController extends Controller
{
    public function index(Request $request)
    {
        $organizationId = $request->user()->organization_id;
        $plans = MonthlyPlan::where('organization_id', $organizationId)
            ->with(['project', 'goals', 'employees'])
            ->latest()
            ->paginate(10);
        
        return view('monthly-plans.index', compact('plans'));
    }

    public function create(Request $request)
    {
        $organizationId = $request->user()->organization_id;
        $projects = Project::where('organization_id', $organizationId)->where('status', 'active')->get();
        $employees = Employee::where('organization_id', $organizationId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
        
        // التحقق من وجود موظفين
        if ($employees->isEmpty()) {
            return redirect()->route('employees.create')
                ->with('info', 'يجب إضافة موظف واحد على الأقل قبل إنشاء خطة شهرية');
        }
        
        return view('monthly-plans.create', compact('projects', 'employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'month' => 'required|string',
            'year' => 'required|integer|min:2020|max:2100',
            'month_number' => 'required|integer|min:1|max:12',
            'description' => 'nullable|string',
            'goals' => 'required|array|min:1',
            'goals.*.goal_type' => 'required|string',
            'goals.*.goal_name' => 'required|string|max:255',
            'goals.*.target_value' => 'required|integer|min:0',
            'goals.*.unit' => 'nullable|string|max:50',
            'goals.*.description' => 'nullable|string',
            'employee_ids' => 'required|array|min:1',
            'employee_ids.*' => 'exists:employees,id',
        ]);

        try {
            DB::beginTransaction();
            
            $organizationId = $request->user()->organization_id;
            
            // التحقق من أن المشروع يتبع المنظمة
            $project = Project::where('id', $request->project_id)
                ->where('organization_id', $organizationId)
                ->firstOrFail();
            
            // إنشاء الخطة الشهرية
            $plan = MonthlyPlan::create([
                'project_id' => $request->project_id,
                'organization_id' => $organizationId,
                'month' => $request->month,
                'year' => $request->year,
                'month_number' => $request->month_number,
                'description' => $request->description,
                'status' => 'draft',
            ]);
            
            // إضافة الأهداف
            foreach ($request->goals as $goalData) {
                MonthlyPlanGoal::create([
                    'monthly_plan_id' => $plan->id,
                    'goal_type' => $goalData['goal_type'],
                    'goal_name' => $goalData['goal_name'],
                    'target_value' => $goalData['target_value'],
                    'achieved_value' => 0,
                    'unit' => $goalData['unit'] ?? null,
                    'description' => $goalData['description'] ?? null,
                ]);
            }
            
            // إضافة الموظفين
            $employeeIds = $request->employee_ids;
            // التحقق من أن الموظفين يتبعون المنظمة
            $validEmployees = Employee::whereIn('id', $employeeIds)
                ->where('organization_id', $organizationId)
                ->pluck('id')
                ->toArray();
            
            $plan->employees()->attach($validEmployees);
            
            DB::commit();
            
            return redirect()->route('monthly-plans.show', $plan)
                ->with('success', 'تم إنشاء الخطة الشهرية بنجاح');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء إنشاء الخطة: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Request $request, MonthlyPlan $monthlyPlan)
    {
        if ($monthlyPlan->organization_id !== $request->user()->organization_id) {
            abort(403);
        }
        
        $monthlyPlan->load(['project', 'goals', 'employees', 'tasks.assignedEmployee']);
        
        // تجميع المهام حسب القائمة (list_type و assigned_to)
        $tasksByList = $monthlyPlan->tasks->groupBy(function($task) {
            // إذا كانت list_type = 'tasks' أو لا يوجد assigned_to، فالقائمة هي 'tasks'
            if ($task->list_type === 'tasks') {
                return 'tasks';
            }
            // إذا كانت list_type = 'employee' و يوجد assigned_to، فالقائمة هي 'employee_{id}'
            if ($task->list_type === 'employee' && $task->assigned_to) {
                return 'employee_' . $task->assigned_to;
            }
            // في حالة وجود أي مشكلة، نضعها في قائمة tasks
            return 'tasks';
        });
        
        return view('monthly-plans.show', compact('monthlyPlan', 'tasksByList'));
    }

    public function edit(Request $request, MonthlyPlan $monthlyPlan)
    {
        if ($monthlyPlan->organization_id !== $request->user()->organization_id) {
            abort(403);
        }
        
        $organizationId = $request->user()->organization_id;
        $projects = Project::where('organization_id', $organizationId)->where('status', 'active')->get();
        $employees = Employee::where('organization_id', $organizationId)->where('status', 'active')->get();
        
        $monthlyPlan->load(['goals', 'employees']);
        
        return view('monthly-plans.edit', compact('monthlyPlan', 'projects', 'employees'));
    }

    public function update(Request $request, MonthlyPlan $monthlyPlan)
    {
        if ($monthlyPlan->organization_id !== $request->user()->organization_id) {
            abort(403);
        }
        
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'month' => 'required|string',
            'year' => 'required|integer|min:2020|max:2100',
            'month_number' => 'required|integer|min:1|max:12',
            'description' => 'nullable|string',
            'status' => 'required|in:draft,active,completed,cancelled',
            'goals' => 'required|array|min:1',
            'goals.*.goal_type' => 'required|string',
            'goals.*.goal_name' => 'required|string|max:255',
            'goals.*.target_value' => 'required|integer|min:0',
            'goals.*.unit' => 'nullable|string|max:50',
            'goals.*.description' => 'nullable|string',
            'employee_ids' => 'required|array|min:1',
            'employee_ids.*' => 'exists:employees,id',
        ]);

        try {
            DB::beginTransaction();
            
            $organizationId = $request->user()->organization_id;
            
            // التحقق من أن المشروع يتبع المنظمة
            $project = Project::where('id', $request->project_id)
                ->where('organization_id', $organizationId)
                ->firstOrFail();
            
            // تحديث الخطة الشهرية
            $monthlyPlan->update($request->only([
                'project_id', 'month', 'year', 'month_number', 'description', 'status'
            ]));
            
            // تحديث الأهداف
            // جمع IDs للأهداف الموجودة المرسلة
            $submittedGoalIds = collect($request->goals)
                ->filter(fn($goal) => isset($goal['id']))
                ->pluck('id')
                ->toArray();
            
            // حذف الأهداف التي لم يتم إرسالها
            $monthlyPlan->goals()
                ->whereNotIn('id', $submittedGoalIds)
                ->delete();
            
            // تحديث الأهداف الموجودة أو إنشاء جديدة
            foreach ($request->goals as $goalData) {
                if (isset($goalData['id'])) {
                    // تحديث هدف موجود
                    $goal = MonthlyPlanGoal::where('id', $goalData['id'])
                        ->where('monthly_plan_id', $monthlyPlan->id)
                        ->first();
                    
                    if ($goal) {
                        $goal->update([
                            'goal_type' => $goalData['goal_type'],
                            'goal_name' => $goalData['goal_name'],
                            'target_value' => $goalData['target_value'],
                            'unit' => $goalData['unit'] ?? null,
                            'description' => $goalData['description'] ?? null,
                        ]);
                    }
                } else {
                    // إنشاء هدف جديد
                    MonthlyPlanGoal::create([
                        'monthly_plan_id' => $monthlyPlan->id,
                        'goal_type' => $goalData['goal_type'],
                        'goal_name' => $goalData['goal_name'],
                        'target_value' => $goalData['target_value'],
                        'achieved_value' => 0,
                        'unit' => $goalData['unit'] ?? null,
                        'description' => $goalData['description'] ?? null,
                    ]);
                }
            }
            
            // تحديث الموظفين
            $employeeIds = $request->employee_ids;
            // التحقق من أن الموظفين يتبعون المنظمة
            $validEmployees = Employee::whereIn('id', $employeeIds)
                ->where('organization_id', $organizationId)
                ->pluck('id')
                ->toArray();
            
            $monthlyPlan->employees()->sync($validEmployees);
            
            DB::commit();
            
            return redirect()->route('monthly-plans.show', $monthlyPlan)
                ->with('success', 'تم تحديث الخطة بنجاح');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تحديث الخطة: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Request $request, MonthlyPlan $monthlyPlan)
    {
        if ($monthlyPlan->organization_id !== $request->user()->organization_id) {
            abort(403);
        }

        $monthlyPlan->delete();

        return redirect()->route('monthly-plans.index')
            ->with('success', 'تم حذف الخطة بنجاح');
    }
}
