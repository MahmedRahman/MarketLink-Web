<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * الحارس الذي نجح تسجيل الدخول من خلاله: web (أدمن/مستخدم) أو employee.
     */
    public string $authenticatedGuard = 'web';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $credentials = $this->only('email', 'password');
        $remember = $this->boolean('remember');

        // 1) محاولة الدخول كمستخدم/أدمن (حارس web)
        if (Auth::guard('web')->attempt($credentials, $remember)) {
            $user = Auth::guard('web')->user();

            if ($user && $user->status === 'suspended') {
                Auth::guard('web')->logout();
                throw ValidationException::withMessages([
                    'email' => 'تم إيقاف حسابك. يرجى التواصل مع الدعم الفني.',
                ]);
            }

            if ($user && $user->status === 'inactive') {
                Auth::guard('web')->logout();
                throw ValidationException::withMessages([
                    'email' => 'حسابك غير نشط. يرجى التواصل مع الدعم الفني.',
                ]);
            }

            $this->authenticatedGuard = 'web';
            RateLimiter::clear($this->throttleKey());

            return;
        }

        // 2) محاولة الدخول كموظف (حارس employee)
        if (Auth::guard('employee')->attempt($credentials, $remember)) {
            $employee = Auth::guard('employee')->user();

            if ($employee && $employee->status === 'inactive') {
                Auth::guard('employee')->logout();
                throw ValidationException::withMessages([
                    'email' => 'حسابك غير نشط. يرجى التواصل مع المدير.',
                ]);
            }

            if ($employee && $employee->status === 'pending') {
                Auth::guard('employee')->logout();
                throw ValidationException::withMessages([
                    'email' => 'حسابك في انتظار الموافقة. يرجى التواصل مع المدير.',
                ]);
            }

            $this->authenticatedGuard = 'employee';
            RateLimiter::clear($this->throttleKey());

            return;
        }

        RateLimiter::hit($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.failed'),
        ]);
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 10)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
