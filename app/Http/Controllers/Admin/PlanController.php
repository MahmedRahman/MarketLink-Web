<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\PlanFeature;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $plans = Plan::with('features')
            ->ordered()
            ->paginate(10);

        // حساب الإحصائيات من جميع الخطط
        $stats = [
            'total' => Plan::count(),
            'active' => Plan::where('is_active', true)->count(),
            'inactive' => Plan::where('is_active', false)->count(),
        ];

        return view('admin.plans.index', compact('plans', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.plans.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price_egp' => ['required', 'numeric', 'min:0'],
            'duration_days' => ['required', 'integer', 'min:1'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer'],
            'features' => ['nullable', 'array'],
            'features.*' => ['required', 'string', 'max:255'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $plan = Plan::create($validated);

        // إضافة الميزات
        if ($request->has('features') && is_array($request->features)) {
            foreach ($request->features as $index => $featureName) {
                if (!empty(trim($featureName))) {
                    PlanFeature::create([
                        'plan_id' => $plan->id,
                        'feature_name' => trim($featureName),
                        'order' => $index,
                    ]);
                }
            }
        }

        return redirect()->route('admin.plans.index')
            ->with('success', 'تم إنشاء الخطة بنجاح!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Plan $plan): View
    {
        $plan->load('features');
        return view('admin.plans.show', compact('plan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Plan $plan): View
    {
        $plan->load('features');
        return view('admin.plans.edit', compact('plan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Plan $plan): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price_egp' => ['required', 'numeric', 'min:0'],
            'duration_days' => ['required', 'integer', 'min:1'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer'],
            'features' => ['nullable', 'array'],
            'features.*' => ['required', 'string', 'max:255'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? $plan->sort_order;

        $plan->update($validated);

        // تحديث الميزات
        if ($request->has('features')) {
            // حذف الميزات القديمة
            $plan->features()->delete();

            // إضافة الميزات الجديدة
            if (is_array($request->features)) {
                foreach ($request->features as $index => $featureName) {
                    if (!empty(trim($featureName))) {
                        PlanFeature::create([
                            'plan_id' => $plan->id,
                            'feature_name' => trim($featureName),
                            'order' => $index,
                        ]);
                    }
                }
            }
        }

        return redirect()->route('admin.plans.index')
            ->with('success', 'تم تحديث الخطة بنجاح!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Plan $plan): RedirectResponse
    {
        // التحقق من وجود اشتراكات مرتبطة
        if ($plan->subscriptions()->count() > 0) {
            return redirect()->route('admin.plans.index')
                ->with('error', 'لا يمكن حذف الخطة لوجود اشتراكات مرتبطة بها!');
        }

        $plan->features()->delete();
        $plan->delete();

        return redirect()->route('admin.plans.index')
            ->with('success', 'تم حذف الخطة بنجاح!');
    }
}
