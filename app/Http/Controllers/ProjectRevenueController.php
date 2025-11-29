<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectRevenue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ProjectRevenueController extends Controller
{
    public function index(Request $request, Project $project)
    {
        $selectedMonth = $request->query('month');
        
        $revenuesQuery = $project->revenues();
        
        // تطبيق فلتر الشهر إن وجد
        if ($selectedMonth) {
            try {
                $monthDate = Carbon::createFromFormat('Y-m', $selectedMonth);
                $startOfMonth = $monthDate->copy()->startOfMonth();
                $endOfMonth = $monthDate->copy()->endOfMonth();
                
                $revenuesQuery->whereBetween('revenue_date', [
                    $startOfMonth->toDateString(),
                    $endOfMonth->toDateString()
                ]);
            } catch (\Exception $e) {
                // في حالة خطأ في التاريخ، نستمر بدون فلتر
                \Log::warning('Error filtering revenues by month: ' . $e->getMessage());
            }
        }
        
        $revenues = $revenuesQuery->latest('revenue_date')->get();
        
        return view('projects.revenues.index', compact('project', 'revenues', 'selectedMonth'));
    }

    public function create(Project $project)
    {
        return view('projects.revenues.create', compact('project'));
    }

    public function store(Request $request, Project $project)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'amount' => 'required|numeric|min:0',
                'paid_amount' => 'nullable|numeric|min:0',
                'currency' => 'required|string|max:3|in:EGP',
                'revenue_date' => 'required|date',
                'record_month_year' => 'nullable|string|max:7|regex:/^\d{4}-\d{2}$/',
                'payment_method' => 'required|in:cash,bank_transfer,credit_card,check,vodafone_cash,instapay,paypal,western_union,other',
                'transfer_image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:5120', // 5MB max
                'status' => 'required|in:pending,received,cancelled',
                'notes' => 'nullable|string'
            ]);

            $data = $request->except('transfer_image');
            
            // إذا لم يتم تحديد record_month_year، استخرجه من revenue_date
            if (empty($data['record_month_year']) && !empty($data['revenue_date'])) {
                $data['record_month_year'] = Carbon::parse($data['revenue_date'])->format('Y-m');
            }

            // رفع صورة التحويل إن وجدت
            if ($request->hasFile('transfer_image')) {
                try {
                    $image = $request->file('transfer_image');
                    
                    // التحقق من أن الملف صحيح
                    if ($image->isValid()) {
                        // التأكد من وجود المجلد
                        $directory = 'revenue_transfers/' . $project->id;
                        if (!Storage::disk('public')->exists($directory)) {
                            Storage::disk('public')->makeDirectory($directory);
                        }
                        
                        $imagePath = $image->store($directory, 'public');
                        $data['transfer_image'] = $imagePath;
                    } else {
                        throw new \Exception('الملف المرفوع غير صحيح');
                    }
                } catch (\Exception $e) {
                    return redirect()->back()
                        ->with('error', 'حدث خطأ أثناء رفع الصورة: ' . $e->getMessage())
                        ->withInput();
                }
            }

            $project->revenues()->create($data);

            return redirect()->route('projects.revenues.index', $project)
                ->with('success', 'تم إضافة الإيراد بنجاح');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء إضافة الإيراد: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Project $project, ProjectRevenue $revenue)
    {
        return view('projects.revenues.show', compact('project', 'revenue'));
    }

    public function edit(Project $project, ProjectRevenue $revenue)
    {
        return view('projects.revenues.edit', compact('project', 'revenue'));
    }

    public function update(Request $request, Project $project, ProjectRevenue $revenue)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'amount' => 'required|numeric|min:0',
                'paid_amount' => 'nullable|numeric|min:0',
                'currency' => 'required|string|max:3|in:EGP',
                'revenue_date' => 'required|date',
                'record_month_year' => 'nullable|string|max:7|regex:/^\d{4}-\d{2}$/',
                'payment_method' => 'required|in:cash,bank_transfer,credit_card,check,vodafone_cash,instapay,paypal,western_union,other',
                'transfer_image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:5120', // 5MB max
                'delete_transfer_image' => 'nullable|boolean',
                'status' => 'required|in:pending,received,cancelled',
                'notes' => 'nullable|string'
            ]);

            $data = $request->except(['transfer_image', 'delete_transfer_image']);
            
            // إذا لم يتم تحديد record_month_year، استخرجه من revenue_date
            if (empty($data['record_month_year']) && !empty($data['revenue_date'])) {
                $data['record_month_year'] = Carbon::parse($data['revenue_date'])->format('Y-m');
            }

            // حذف الصورة القديمة إذا طلب المستخدم ذلك
            $shouldDeleteImage = $request->has('delete_transfer_image') && $request->delete_transfer_image == '1';
            
            if ($shouldDeleteImage && $revenue->transfer_image) {
                try {
                    if (Storage::disk('public')->exists($revenue->transfer_image)) {
                        Storage::disk('public')->delete($revenue->transfer_image);
                    }
                    $data['transfer_image'] = null;
                } catch (\Exception $e) {
                    // لا نوقف العملية إذا فشل حذف الصورة
                    \Log::warning('Failed to delete old transfer image: ' . $e->getMessage());
                }
            }

            // رفع صورة التحويل الجديدة إن وجدت
            if ($request->hasFile('transfer_image')) {
                try {
                    $image = $request->file('transfer_image');
                    
                    // التحقق من أن الملف صحيح
                    if ($image->isValid()) {
                        // حذف الصورة القديمة إن وجدت (فقط إذا لم يكن المستخدم قد حذفها بالفعل)
                        if (!$shouldDeleteImage && $revenue->transfer_image && Storage::disk('public')->exists($revenue->transfer_image)) {
                            Storage::disk('public')->delete($revenue->transfer_image);
                        }
                        
                        // التأكد من وجود المجلد
                        $directory = 'revenue_transfers/' . $project->id;
                        if (!Storage::disk('public')->exists($directory)) {
                            Storage::disk('public')->makeDirectory($directory, 0755, true);
                        }
                        
                        $imagePath = $image->store($directory, 'public');
                        $data['transfer_image'] = $imagePath;
                    } else {
                        throw new \Exception('الملف المرفوع غير صحيح أو تالف');
                    }
                } catch (\Exception $e) {
                    return redirect()->back()
                        ->with('error', 'حدث خطأ أثناء رفع الصورة: ' . $e->getMessage())
                        ->withInput();
                }
            }

            $revenue->update($data);

            return redirect()->route('projects.revenues.index', $project)
                ->with('success', 'تم تحديث الإيراد بنجاح');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تحديث الإيراد: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Project $project, ProjectRevenue $revenue)
    {
        try {
            $revenue->delete();
            return redirect()->route('projects.revenues.index', $project)
                ->with('success', 'تم حذف الإيراد بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء حذف الإيراد: ' . $e->getMessage());
        }
    }

    /**
     * Duplicate an existing revenue
     */
    public function duplicate(Project $project, ProjectRevenue $revenue)
    {
        try {
            // Create a new revenue with the same data (except id and timestamps)
            $newRevenue = $revenue->replicate();
            $newRevenue->title = $revenue->title . ' (نسخة)';
            $newRevenue->paid_amount = 0; // Reset paid amount for duplicate
            $newRevenue->status = 'pending'; // Set status to pending for duplicate
            $newRevenue->transfer_image = null; // Don't copy the image
            $newRevenue->save();

            return redirect()->route('projects.revenues.index', $project)
                ->with('success', 'تم نسخ الإيراد بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء نسخ الإيراد: ' . $e->getMessage());
        }
    }

    /**
     * عرض جميع إيرادات المشاريع
     */
    public function all(Request $request)
    {
        $user = $request->user();
        $organizationId = $user->organization_id;

        // جلب جميع الإيرادات للمشاريع التابعة للمنظمة
        $revenuesQuery = ProjectRevenue::with('project')
            ->whereHas('project', function($query) use ($organizationId) {
                $query->where('organization_id', $organizationId);
            });

        // فلترة حسب المشروع
        if ($request->has('project_id') && $request->project_id) {
            $revenuesQuery->where('project_id', $request->project_id);
        }

        // فلترة حسب الشهر (تاريخ الإيراد)
        if ($request->has('month') && $request->month) {
            try {
                $monthDate = Carbon::createFromFormat('Y-m', $request->month);
                $startOfMonth = $monthDate->copy()->startOfMonth();
                $endOfMonth = $monthDate->copy()->endOfMonth();
                
                $revenuesQuery->whereBetween('revenue_date', [
                    $startOfMonth->toDateString(),
                    $endOfMonth->toDateString()
                ]);
            } catch (\Exception $e) {
                \Log::warning('Error filtering revenues by month: ' . $e->getMessage());
            }
        }

        // فلترة حسب السجلات الشهرية
        if ($request->has('record_month_year') && $request->record_month_year) {
            $revenuesQuery->where('record_month_year', $request->record_month_year);
        }

        // فلترة حسب الحالة
        if ($request->has('status') && $request->status) {
            $revenuesQuery->where('status', $request->status);
        }

        $revenues = $revenuesQuery->latest('revenue_date')->get();
        
        // جلب جميع المشاريع للمنظمة للفلترة
        $projects = Project::where('organization_id', $organizationId)
            ->orderBy('business_name')
            ->get();

        // جلب جميع السجلات الشهرية الفريدة مع عدد السجلات لكل شهر
        $monthlyRecordsData = ProjectRevenue::whereHas('project', function($query) use ($organizationId) {
                $query->where('organization_id', $organizationId);
            })
            ->whereNotNull('record_month_year')
            ->select('record_month_year')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('record_month_year')
            ->orderBy('record_month_year', 'desc')
            ->get();

        $monthlyRecords = $monthlyRecordsData->map(function($item) {
            return [
                'record_month_year' => $item->record_month_year,
                'count' => $item->count
            ];
        });

        // حساب إجمالي عدد السجلات
        $totalCount = ProjectRevenue::whereHas('project', function($query) use ($organizationId) {
            $query->where('organization_id', $organizationId);
        })->count();

        return view('projects.revenues.all', compact('revenues', 'projects', 'monthlyRecords', 'totalCount'));
    }

    /**
     * عرض صفحة إضافة إيراد جديد (لجميع المشاريع)
     */
    public function createAll(Request $request)
    {
        $user = $request->user();
        $organizationId = $user->organization_id;
        
        $projects = Project::where('organization_id', $organizationId)
            ->orderBy('business_name')
            ->get();

        return view('projects.revenues.create-all', compact('projects'));
    }

    /**
     * حفظ إيراد جديد (لجميع المشاريع)
     */
    public function storeAll(Request $request)
    {
        try {
            $request->validate([
                'project_id' => 'required|exists:projects,id',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'amount' => 'required|numeric|min:0',
                'paid_amount' => 'nullable|numeric|min:0',
                'currency' => 'required|string|max:3|in:EGP',
                'revenue_date' => 'required|date',
                'record_month_year' => 'nullable|string|max:7|regex:/^\d{4}-\d{2}$/',
                'payment_method' => 'required|in:cash,bank_transfer,credit_card,check,vodafone_cash,instapay,paypal,western_union,other',
                'transfer_image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:5120',
                'status' => 'required|in:pending,received,cancelled',
                'notes' => 'nullable|string'
            ]);

            $project = Project::findOrFail($request->project_id);
            
            // التحقق من أن المشروع تابع للمنظمة
            if ($project->organization_id !== $request->user()->organization_id) {
                abort(403);
            }

            $data = $request->except('transfer_image');
            
            // إذا لم يتم تحديد record_month_year، استخرجه من revenue_date
            if (empty($data['record_month_year']) && !empty($data['revenue_date'])) {
                $data['record_month_year'] = Carbon::parse($data['revenue_date'])->format('Y-m');
            }

            // رفع صورة التحويل إن وجدت
            if ($request->hasFile('transfer_image')) {
                try {
                    $image = $request->file('transfer_image');
                    
                    if ($image->isValid()) {
                        $directory = 'revenue_transfers/' . $project->id;
                        if (!Storage::disk('public')->exists($directory)) {
                            Storage::disk('public')->makeDirectory($directory);
                        }
                        
                        $imagePath = $image->store($directory, 'public');
                        $data['transfer_image'] = $imagePath;
                    } else {
                        throw new \Exception('الملف المرفوع غير صحيح');
                    }
                } catch (\Exception $e) {
                    return redirect()->back()
                        ->with('error', 'حدث خطأ أثناء رفع الصورة: ' . $e->getMessage())
                        ->withInput();
                }
            }

            $project->revenues()->create($data);

            return redirect()->route('revenues.all')
                ->with('success', 'تم إضافة الإيراد بنجاح');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء إضافة الإيراد: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * عرض صفحة تعديل إيراد (لجميع المشاريع)
     */
    public function editAll(Request $request, ProjectRevenue $revenue)
    {
        // التحقق من أن الإيراد تابع لمشروع في منظمة المستخدم
        if ($revenue->project->organization_id !== $request->user()->organization_id) {
            abort(403);
        }

        $user = $request->user();
        $organizationId = $user->organization_id;
        
        $projects = Project::where('organization_id', $organizationId)
            ->orderBy('business_name')
            ->get();

        return view('projects.revenues.edit-all', compact('revenue', 'projects'));
    }

    /**
     * تحديث إيراد (لجميع المشاريع)
     */
    public function updateAll(Request $request, ProjectRevenue $revenue)
    {
        try {
            // التحقق من أن الإيراد تابع لمشروع في منظمة المستخدم
            if ($revenue->project->organization_id !== $request->user()->organization_id) {
                abort(403);
            }

            $request->validate([
                'project_id' => 'required|exists:projects,id',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'amount' => 'required|numeric|min:0',
                'paid_amount' => 'nullable|numeric|min:0',
                'currency' => 'required|string|max:3|in:EGP',
                'revenue_date' => 'required|date',
                'record_month_year' => 'nullable|string|max:7|regex:/^\d{4}-\d{2}$/',
                'payment_method' => 'required|in:cash,bank_transfer,credit_card,check,vodafone_cash,instapay,paypal,western_union,other',
                'transfer_image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:5120',
                'delete_transfer_image' => 'nullable|boolean',
                'status' => 'required|in:pending,received,cancelled',
                'notes' => 'nullable|string'
            ]);

            $project = Project::findOrFail($request->project_id);
            
            // التحقق من أن المشروع تابع للمنظمة
            if ($project->organization_id !== $request->user()->organization_id) {
                abort(403);
            }

            $data = $request->except(['transfer_image', 'delete_transfer_image']);
            
            // إذا لم يتم تحديد record_month_year، استخرجه من revenue_date
            if (empty($data['record_month_year']) && !empty($data['revenue_date'])) {
                $data['record_month_year'] = Carbon::parse($data['revenue_date'])->format('Y-m');
            }

            // حذف الصورة القديمة إذا طلب المستخدم ذلك
            $shouldDeleteImage = $request->has('delete_transfer_image') && $request->delete_transfer_image == '1';
            
            if ($shouldDeleteImage && $revenue->transfer_image) {
                try {
                    if (Storage::disk('public')->exists($revenue->transfer_image)) {
                        Storage::disk('public')->delete($revenue->transfer_image);
                    }
                    $data['transfer_image'] = null;
                } catch (\Exception $e) {
                    \Log::warning('Failed to delete old transfer image: ' . $e->getMessage());
                }
            }

            // رفع صورة التحويل الجديدة إن وجدت
            if ($request->hasFile('transfer_image')) {
                try {
                    $image = $request->file('transfer_image');
                    
                    if ($image->isValid()) {
                        if (!$shouldDeleteImage && $revenue->transfer_image && Storage::disk('public')->exists($revenue->transfer_image)) {
                            Storage::disk('public')->delete($revenue->transfer_image);
                        }
                        
                        $directory = 'revenue_transfers/' . $project->id;
                        if (!Storage::disk('public')->exists($directory)) {
                            Storage::disk('public')->makeDirectory($directory, 0755, true);
                        }
                        
                        $imagePath = $image->store($directory, 'public');
                        $data['transfer_image'] = $imagePath;
                    } else {
                        throw new \Exception('الملف المرفوع غير صحيح أو تالف');
                    }
                } catch (\Exception $e) {
                    return redirect()->back()
                        ->with('error', 'حدث خطأ أثناء رفع الصورة: ' . $e->getMessage())
                        ->withInput();
                }
            }

            $revenue->update($data);

            return redirect()->route('revenues.all')
                ->with('success', 'تم تحديث الإيراد بنجاح');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تحديث الإيراد: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * حذف إيراد (لجميع المشاريع)
     */
    public function destroyAll(Request $request, ProjectRevenue $revenue)
    {
        try {
            // التحقق من أن الإيراد تابع لمشروع في منظمة المستخدم
            if ($revenue->project->organization_id !== $request->user()->organization_id) {
                abort(403);
            }

            $revenue->delete();
            return redirect()->route('revenues.all')
                ->with('success', 'تم حذف الإيراد بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء حذف الإيراد: ' . $e->getMessage());
        }
    }

    /**
     * نسخ إيراد (لجميع المشاريع)
     */
    public function duplicateAll(Request $request, ProjectRevenue $revenue)
    {
        try {
            // التحقق من أن الإيراد تابع لمشروع في منظمة المستخدم
            if ($revenue->project->organization_id !== $request->user()->organization_id) {
                abort(403);
            }

            $newRevenue = $revenue->replicate();
            $newRevenue->title = $revenue->title . ' (نسخة)';
            $newRevenue->paid_amount = 0;
            $newRevenue->status = 'pending';
            $newRevenue->transfer_image = null;
            $newRevenue->save();

            return redirect()->route('revenues.all')
                ->with('success', 'تم نسخ الإيراد بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء نسخ الإيراد: ' . $e->getMessage());
        }
    }
}