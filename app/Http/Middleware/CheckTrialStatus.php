<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTrialStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->organization) {
            return redirect()->route('login');
        }

        // السماح بالوصول إذا كانت هناك اشتراك نشط
        if ($user->organizationHasActiveSubscription()) {
            return $next($request);
        }

        // السماح بالوصول إذا كانت في فترة Trial
        if ($user->organizationIsOnTrial()) {
            return $next($request);
        }

        // إذا انتهت فترة Trial ولم يكن هناك اشتراك نشط، إعادة توجيه لصفحة الاشتراك
        if ($user->organizationTrialExpired()) {
            return redirect()->route('subscription.expired')
                ->with('error', 'انتهت فترة التجربة المجانية. يرجى الاشتراك للاستمرار في استخدام النظام.');
        }

        // إذا لم يكن هناك اشتراك أو trial، إعادة توجيه لصفحة الاشتراك
        return redirect()->route('subscription.expired')
            ->with('error', 'يجب الاشتراك للاستمرار في استخدام النظام.');
    }
}
