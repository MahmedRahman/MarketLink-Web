<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    /**
     * عرض لوحة تحكم المدير
     */
    public function dashboard(): View
    {
        $stats = [
            'total_users' => User::where('is_admin', false)->count(),
            'total_organizations' => Organization::count(),
            'active_subscriptions' => Subscription::where('status', 'active')->count(),
            'trial_subscriptions' => Subscription::where('status', 'trial')->count(),
            'expired_subscriptions' => Subscription::where('status', 'expired')->count(),
        ];

        $recent_users = User::where('is_admin', false)
            ->with('organization')
            ->latest()
            ->take(5)
            ->get();

        $recent_organizations = Organization::with(['subscription' => function($q) {
            $q->whereIn('status', ['trial', 'active'])->latest();
        }])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_users', 'recent_organizations'));
    }

    /**
     * عرض جميع المستخدمين
     */
    public function users(Request $request): View
    {
        $query = User::with(['organization.clients', 'organization.employees', 'organization.projects']);

        // فلترة حسب نوع المستخدم
        if ($request->has('user_type')) {
            if ($request->user_type === 'admin') {
                $query->where('is_admin', true);
            } elseif ($request->user_type === 'organization') {
                $query->where('is_admin', false);
            }
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    /**
     * عرض تفاصيل مستخدم
     */
    public function showUser(User $user): View
    {
        // تحميل العلاقات حسب نوع المستخدم
        if (!$user->is_admin) {
            $user->load('organization.subscription');
        }

        return view('admin.users.show', compact('user'));
    }

    /**
     * عرض جميع المنظمات
     */
    public function organizations(Request $request): View
    {
        $query = Organization::with(['subscription' => function($q) {
            $q->whereIn('status', ['trial', 'active'])->latest();
        }, 'users']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $organizations = $query->latest()->paginate(15);

        return view('admin.organizations.index', compact('organizations'));
    }

    /**
     * عرض تفاصيل منظمة
     */
    public function showOrganization(Organization $organization): View
    {
        $organization->load(['users', 'subscription']);

        return view('admin.organizations.show', compact('organization'));
    }

    /**
     * عرض جميع الاشتراكات
     */
    public function subscriptions(Request $request): View
    {
        $query = Subscription::with('organization');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('plan')) {
            $query->where('plan', $request->plan);
        }

        $subscriptions = $query->latest()->paginate(15);

        // حساب الإحصائيات من جميع الاشتراكات (غير مفلترة)
        $stats = [
            'total' => Subscription::count(),
            'active' => Subscription::where('status', 'active')->count(),
            'trial' => Subscription::where('status', 'trial')->count(),
            'expired' => Subscription::where('status', 'expired')->count(),
        ];

        return view('admin.subscriptions.index', compact('subscriptions', 'stats'));
    }

    /**
     * عرض تفاصيل اشتراك
     */
    public function showSubscription(Subscription $subscription): View
    {
        $subscription->load('organization.users');

        return view('admin.subscriptions.show', compact('subscription'));
    }

    /**
     * تحديث حالة الاشتراك
     */
    public function updateSubscription(Request $request, Subscription $subscription): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:trial,active,expired,cancelled',
            'plan' => 'required|in:trial,basic,professional,enterprise',
            'ends_at' => 'nullable|date',
        ]);

        $subscription->update([
            'status' => $request->status,
            'plan' => $request->plan,
            'ends_at' => $request->ends_at ? now()->parse($request->ends_at) : $subscription->ends_at,
        ]);

        return redirect()->route('admin.subscriptions.show', $subscription)
            ->with('success', 'تم تحديث الاشتراك بنجاح');
    }

    /**
     * حذف منظمة
     */
    public function deleteOrganization(Organization $organization): RedirectResponse
    {
        $organization->delete();

        return redirect()->route('admin.organizations.index')
            ->with('success', 'تم حذف المنظمة بنجاح');
    }

    /**
     * حذف مستخدم
     */
    public function deleteUser(User $user): RedirectResponse
    {
        if ($user->is_admin) {
            abort(403);
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'تم حذف المستخدم بنجاح');
    }

    /**
     * تحديث حالة المستخدم
     */
    public function updateUserStatus(Request $request, User $user): RedirectResponse
    {
        if ($user->is_admin) {
            abort(403, 'لا يمكن تغيير حالة المدير');
        }

        $request->validate([
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $user->update([
            'status' => $request->status,
        ]);

        $statusText = match($request->status) {
            'active' => 'نشط',
            'inactive' => 'غير نشط',
            'suspended' => 'موقوف',
            default => $request->status,
        };

        return redirect()->back()
            ->with('success', "تم تحديث حالة المستخدم إلى: {$statusText}");
    }

    /**
     * تحديث كلمة مرور المستخدم
     */
    public function updateUserPassword(Request $request, User $user): RedirectResponse
    {
        if ($user->is_admin) {
            abort(403, 'لا يمكن تغيير كلمة مرور المدير');
        }

        $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()
            ->with('success', 'تم تحديث كلمة المرور بنجاح');
    }

    /**
     * الدخول كالمستخدم (Impersonate)
     */
    public function impersonate(User $user): RedirectResponse
    {
        // منع الدخول كمدير آخر
        if ($user->is_admin) {
            abort(403, 'لا يمكن الدخول كمدير آخر');
        }

        // حفظ معلومات المدير الحالي في session
        session()->put('admin_id', Auth::id());
        session()->put('impersonating', true);

        // تسجيل الدخول كالمستخدم المحدد
        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('success', "تم الدخول كالمستخدم: {$user->name}");
    }

    /**
     * العودة للمدير (Stop Impersonating)
     */
    public function stopImpersonating(): RedirectResponse
    {
        if (!session('impersonating')) {
            // إذا لم يكن في وضع impersonation، إعادة توجيه حسب نوع المستخدم
            if (Auth::check() && Auth::user()->is_admin) {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('dashboard');
        }

        $adminId = session('admin_id');
        session()->forget(['admin_id', 'impersonating']);

        if ($adminId) {
            $admin = User::find($adminId);
            if ($admin && $admin->is_admin) {
                Auth::login($admin);
                return redirect()->route('admin.users.index')
                    ->with('success', 'تم العودة إلى حساب المدير');
            }
        }

        // في حالة فشل استعادة المدير، إعادة توجيه للصفحة الرئيسية
        return redirect()->route('dashboard')
            ->with('error', 'حدث خطأ أثناء العودة إلى حساب المدير');
    }
}
