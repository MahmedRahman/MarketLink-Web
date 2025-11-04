<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeProjectController extends Controller
{
    /**
     * Display a listing of the employee's projects.
     */
    public function index()
    {
        $employee = Auth::guard('employee')->user();
        
        $projects = $employee->projects()
            ->with(['client', 'employees'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('employee.projects.index', compact('projects'));
    }

    /**
     * Display the specified project.
     */
    public function show($projectId)
    {
        $employee = Auth::guard('employee')->user();
        
        $project = $employee->projects()
            ->with(['client', 'employees', 'revenues', 'expenses'])
            ->findOrFail($projectId);

        // جلب الإيصالات الخاصة بهذا الموظف في هذا المشروع
        $employeeExpenses = $project->expenses()
            ->where('employee_id', $employee->id)
            ->orderBy('expense_date', 'desc')
            ->get();

        return view('employee.projects.show', compact('project', 'employeeExpenses'));
    }
}
