<?php

namespace App\Support;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkHub
{
    public static function actor(Request $request): User|Employee|null
    {
        // على مسارات الموظف فضّل employee guard.
        // مهم مع "الدخول كموظف" لأن web + employee بيكونوا مسجّلين معًا،
        // ولو خدنا web الأول ممكن organization_id يطلع لشركة تانية → 403.
        if ($request->routeIs('employee.*')) {
            return Auth::guard('employee')->user()
                ?? Auth::guard('web')->user();
        }

        return Auth::guard('web')->user()
            ?? Auth::guard('employee')->user();
    }

    public static function organizationId(Request $request): ?int
    {
        $actor = self::actor($request);

        return $actor?->organization_id;
    }

    public static function isEmployeeHub(?Request $request = null): bool
    {
        $request ??= request();

        return $request->routeIs('employee.hub.*');
    }

    public static function routePrefix(?Request $request = null): string
    {
        return self::isEmployeeHub($request) ? 'employee.hub' : 'work';
    }

    public static function routeName(string $name, ?Request $request = null): string
    {
        return self::routePrefix($request).'.'.$name;
    }

    public static function layout(?Request $request = null): string
    {
        return self::isEmployeeHub($request) ? 'layouts.employee' : 'layouts.dashboard';
    }

    /**
     * معرف مستخدم الويب فقط (للسجلات والرفع). الموظف لا يملك users.id.
     */
    public static function webUserId(Request $request): ?int
    {
        return Auth::guard('web')->id();
    }

    public static function shareContext(?Request $request = null): void
    {
        $request ??= request();

        view()->share('workLayout', self::layout($request));
        view()->share('workRoutePrefix', self::routePrefix($request));
    }

    public static function authorizeOrganization(Request $request, int $organizationId): void
    {
        abort_unless(self::organizationId($request) === $organizationId, 403);
    }
}
