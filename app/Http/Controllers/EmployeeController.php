<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $organizationId = $request->user()->organization_id;
        
        // الحصول على جميع الموظفين لعرض الأرقام في الكروت
        $allEmployees = Employee::where('organization_id', $organizationId)->get();
        
        // فلترة حسب الدور الوظيفي للجدول
        $employeesQuery = Employee::where('organization_id', $organizationId);
        
        if ($request->has('role') && $request->role) {
            $employeesQuery->where('role', $request->role);
        }
        
        $employees = $employeesQuery->latest()->get();
        
        return view('employees.index', compact('employees', 'allEmployees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('employees.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => ['nullable', 'string', 'max:20', 'unique:employees,phone'],
            'email' => 'required|email|unique:employees,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:content_writer,ad_manager,designer,video_editor,page_manager,account_manager,monitor,media_buyer',
            'status' => 'required|in:active,inactive,pending',
            'instapay_number' => 'nullable|string|max:255',
            'vodafone_cash_number' => 'nullable|string|max:255',
            'facebook_url' => 'nullable|url|max:500',
            'linkedin_url' => 'nullable|url|max:500',
            'portfolio_url' => 'nullable|url|max:500',
            'notes' => 'nullable|string|max:1000',
        ], [
            'phone.unique' => 'رقم الهاتف مستخدم بالفعل',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل',
        ]);

        $data = $request->all();
        $data['password'] = Hash::make($request->password);
        $data['organization_id'] = $request->user()->organization_id;

        Employee::create($data);

        return redirect()->route('employees.index')
            ->with('success', 'تم إضافة الموظف بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Employee $employee)
    {
        if ($employee->organization_id !== $request->user()->organization_id) {
            abort(403);
        }

        return view('employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Employee $employee)
    {
        if ($employee->organization_id !== $request->user()->organization_id) {
            abort(403);
        }

        return view('employees.edit', compact('employee'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        if ($employee->organization_id !== $request->user()->organization_id) {
            abort(403);
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => ['nullable', 'string', 'max:20', 'unique:employees,phone,' . $employee->id],
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:content_writer,ad_manager,designer,video_editor,page_manager,account_manager,monitor,media_buyer',
            'status' => 'required|in:active,inactive,pending',
            'instapay_number' => 'nullable|string|max:255',
            'vodafone_cash_number' => 'nullable|string|max:255',
            'facebook_url' => 'nullable|url|max:500',
            'linkedin_url' => 'nullable|url|max:500',
            'portfolio_url' => 'nullable|url|max:500',
            'notes' => 'nullable|string|max:1000',
        ], [
            'phone.unique' => 'رقم الهاتف مستخدم بالفعل',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل',
        ]);

        $data = $request->all();
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']);
        }

        $employee->update($data);

        return redirect()->route('employees.index')
            ->with('success', 'تم تحديث الموظف بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Employee $employee)
    {
        if ($employee->organization_id !== $request->user()->organization_id) {
            abort(403);
        }

        $employee->delete();

        return redirect()->route('employees.index')
            ->with('success', 'تم حذف الموظف بنجاح');
    }
}
