<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class EmployeeProfileController extends Controller
{
    /**
     * عرض صفحة الملف الشخصي للموظف
     */
    public function edit(): View
    {
        $employee = Auth::guard('employee')->user();
        return view('employee.profile.edit', compact('employee'));
    }

    /**
     * تحديث بيانات الملف الشخصي للموظف
     */
    public function update(Request $request): RedirectResponse
    {
        $employee = Auth::guard('employee')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'instapay_number' => 'nullable|string|max:255',
            'vodafone_cash_number' => 'nullable|string|max:255',
            'facebook_url' => 'nullable|url|max:500',
            'linkedin_url' => 'nullable|url|max:500',
            'portfolio_url' => 'nullable|url|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        $employee->update($request->only([
            'name',
            'phone',
            'email',
            'instapay_number',
            'vodafone_cash_number',
            'facebook_url',
            'linkedin_url',
            'portfolio_url',
            'notes',
        ]));

        return redirect()->route('employee.profile.edit')
            ->with('success', 'تم تحديث بيانات الملف الشخصي بنجاح');
    }

    /**
     * تحديث كلمة المرور
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $employee = Auth::guard('employee')->user();

        if (!Hash::check($request->current_password, $employee->password)) {
            return redirect()->back()
                ->withErrors(['current_password' => 'كلمة المرور الحالية غير صحيحة'])
                ->withInput();
        }

        $employee->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('employee.profile.edit')
            ->with('success', 'تم تحديث كلمة المرور بنجاح');
    }
}

