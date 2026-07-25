<?php

namespace App\Http\Controllers\Employee\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\Auth\EmployeeLoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeAuthController extends Controller
{
    /**
     * تم دمج تسجيل دخول الموظف في صفحة الدخول الموحدة، لذا نوجّه للرابط الموحد.
     */
    public function create(): RedirectResponse
    {
        return redirect()->route('login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(EmployeeLoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('employee.dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('employee')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('employee.login');
    }
}
