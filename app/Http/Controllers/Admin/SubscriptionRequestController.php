<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SubscriptionRequestController extends Controller
{
    /**
     * Display a listing of subscription requests.
     */
    public function index(Request $request): View
    {
        $query = SubscriptionRequest::with(['organization', 'plan', 'approver'])
            ->latest();

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('organization', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $requests = $query->paginate(15);

        $stats = [
            'pending' => SubscriptionRequest::pending()->count(),
            'approved' => SubscriptionRequest::approved()->count(),
            'rejected' => SubscriptionRequest::rejected()->count(),
            'total' => SubscriptionRequest::count(),
        ];

        return view('admin.subscription-requests.index', compact('requests', 'stats'));
    }

    /**
     * Display the specified subscription request.
     */
    public function show(SubscriptionRequest $subscriptionRequest): View
    {
        $subscriptionRequest->load(['organization.users', 'plan.features', 'approver']);
        return view('admin.subscription-requests.show', compact('subscriptionRequest'));
    }

    /**
     * Approve a subscription request.
     */
    public function approve(Request $request, SubscriptionRequest $subscriptionRequest): RedirectResponse
    {
        if ($subscriptionRequest->status !== 'pending') {
            return redirect()->route('admin.subscription-requests.index')
                ->with('error', 'لا يمكن الموافقة على هذا الطلب.');
        }

        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($subscriptionRequest, $validated) {
            // إنشاء الاشتراك
            Subscription::create([
                'organization_id' => $subscriptionRequest->organization_id,
                'plan_id' => $subscriptionRequest->plan_id,
                'status' => 'active',
                'plan' => $subscriptionRequest->plan->slug,
                'starts_at' => now(),
                'ends_at' => now()->addDays($subscriptionRequest->plan->duration_days),
            ]);

            // تحديث حالة الطلب
            $subscriptionRequest->update([
                'status' => 'approved',
                'notes' => $validated['notes'] ?? null,
                'approved_at' => now(),
                'approved_by' => auth()->id(),
            ]);
        });

        return redirect()->route('admin.subscription-requests.index')
            ->with('success', 'تم الموافقة على طلب الاشتراك وإنشاء الاشتراك بنجاح!');
    }

    /**
     * Reject a subscription request.
     */
    public function reject(Request $request, SubscriptionRequest $subscriptionRequest): RedirectResponse
    {
        if ($subscriptionRequest->status !== 'pending') {
            return redirect()->route('admin.subscription-requests.index')
                ->with('error', 'لا يمكن رفض هذا الطلب.');
        }

        $validated = $request->validate([
            'notes' => 'required|string|max:1000',
        ]);

        $subscriptionRequest->update([
            'status' => 'rejected',
            'notes' => $validated['notes'],
            'rejected_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        return redirect()->route('admin.subscription-requests.index')
            ->with('success', 'تم رفض طلب الاشتراك بنجاح!');
    }
}
