<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\SubscriptionRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SubscriptionController extends Controller
{
    /**
     * عرض صفحة حالة الاشتراك
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $organization = $user->organization;
        $subscription = $organization ? $organization->activeSubscription() : null;
        $pendingRequest = $organization ? $organization->subscriptionRequests()->pending()->latest()->first() : null;
        
        if ($subscription) {
            $subscription->load('planModel');
        }
        
        if ($pendingRequest) {
            $pendingRequest->load('plan');
        }

        return view('subscription.index', compact('organization', 'subscription', 'pendingRequest'));
    }

    /**
     * عرض صفحة انتهاء فترة Trial
     */
    public function expired(Request $request): View
    {
        $user = $request->user();
        $organization = $user->organization;
        $subscription = $organization ? $organization->subscription()->latest()->first() : null;
        
        if ($subscription) {
            $subscription->load('planModel');
        }

        return view('subscription.expired', compact('organization', 'subscription'));
    }

    /**
     * عرض خطط الاشتراك
     */
    public function plans(): View
    {
        $plans = Plan::with('features')
            ->active()
            ->ordered()
            ->get();

        return view('subscription.plans', compact('plans'));
    }

    /**
     * معالجة طلب الاشتراك (يمكن ربطه بنظام دفع لاحقاً)
     */
    public function subscribe(Request $request): RedirectResponse
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        $user = $request->user();
        $organization = $user->organization;

        if (!$organization) {
            return redirect()->route('subscription.plans')
                ->with('error', 'حدث خطأ. يرجى المحاولة مرة أخرى.');
        }

        $plan = Plan::findOrFail($request->plan_id);

        if (!$plan->is_active) {
            return redirect()->route('subscription.plans')
                ->with('error', 'الخطة المختارة غير متاحة حالياً.');
        }

        // التحقق من وجود طلب معلق بالفعل
        $pendingRequest = SubscriptionRequest::where('organization_id', $organization->id)
            ->where('status', 'pending')
            ->first();

        if ($pendingRequest) {
            return redirect()->route('subscription.index')
                ->with('error', 'لديك طلب اشتراك قيد المراجعة. يرجى الانتظار حتى يتم مراجعته.');
        }

        // إنشاء طلب اشتراك جديد
        SubscriptionRequest::create([
            'organization_id' => $organization->id,
            'plan_id' => $plan->id,
            'status' => 'pending',
        ]);

        return redirect()->route('subscription.index')
            ->with('success', 'تم إرسال طلب الاشتراك بنجاح! سيتم مراجعته من قبل الإدارة.');
    }
}
