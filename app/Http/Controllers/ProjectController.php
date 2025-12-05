<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectFile;
use App\Models\ProjectRevenue;
use App\Models\ProjectExpense;
use App\Models\Client;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $organizationId = $request->user()->organization_id;
        $selectedResponsibility = $request->get('responsibility');
        
        $projectsQuery = Project::where('organization_id', $organizationId)
            ->with('client')
            ->withCount(['revenues', 'expenses', 'employees']);

        // فلترة حسب المسؤولية
        if ($selectedResponsibility && $selectedResponsibility !== 'all') {
            $projectsQuery->whereJsonContains('responsibility', $selectedResponsibility);
        }

        $projects = $projectsQuery->latest()->paginate(10);

        // حساب عدد المشاريع لكل مسؤولية
        $allProjects = Project::where('organization_id', $organizationId)->get();
        
        $responsibilityStats = [
            'full_management' => 0,
            'media_buyer' => 0,
            'account_manager' => 0,
            'design' => 0,
            'total' => $allProjects->count()
        ];

        foreach ($allProjects as $project) {
            $responsibilities = $project->responsibility ?? [];
            if (is_array($responsibilities)) {
                foreach ($responsibilities as $resp) {
                    if (isset($responsibilityStats[$resp])) {
                        $responsibilityStats[$resp]++;
                    }
                }
            }
        }

        return view('projects.index', compact('projects', 'responsibilityStats', 'selectedResponsibility'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $organizationId = $request->user()->organization_id;
        $clients = Client::where('organization_id', $organizationId)->where('status', 'active')->get();
        $selectedClientId = $request->get('client_id');
        return view('projects.create', compact('clients', 'selectedClientId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'client_id' => 'required|exists:clients,id',
                'business_name' => 'required|string|max:255',
                'business_description' => 'nullable|string',
                'website_url' => 'nullable|url',
                'facebook_url' => 'nullable|url',
                'instagram_url' => 'nullable|url',
                'twitter_url' => 'nullable|url',
                'linkedin_url' => 'nullable|url',
                'youtube_url' => 'nullable|url',
                'tiktok_url' => 'nullable|url',
                'status' => 'required|in:active,inactive,pending',
                'responsibility' => 'nullable|array',
                'responsibility.*' => 'in:full_management,media_buyer,account_manager,design',
                'authorized_persons' => 'nullable|array',
                'authorized_persons.*.name' => 'nullable|string|max:255',
                'authorized_persons.*.phone' => 'nullable|string|max:20',
                'project_accounts' => 'nullable|array',
                'project_accounts.*.account_type' => 'nullable|string|max:255',
                'project_accounts.*.username' => 'nullable|string|max:255',
                'project_accounts.*.password' => 'nullable|string|max:255',
                'project_accounts.*.url' => 'nullable|string|max:500',
                'project_accounts.*.notes' => 'nullable|string|max:1000',
                'employee_ids' => 'nullable|array',
                'employee_ids.*' => 'exists:employees,id',
                'manager_ids' => 'nullable|array',
                'manager_ids.*' => 'exists:employees,id',
                'files' => 'nullable|array',
                'files.*' => 'file|max:10240', // 10MB max per file
                'file_descriptions' => 'nullable|array',
                'file_descriptions.*' => 'nullable|string|max:500',
            ]);

            // تحضير البيانات للـ JSON fields
            $data = $request->all();
            $data['organization_id'] = $request->user()->organization_id;
            
            // تنظيف البيانات الفارغة للمسؤولية
            if (isset($data['responsibility']) && is_array($data['responsibility'])) {
                $data['responsibility'] = array_filter($data['responsibility'], function($item) {
                    return !empty($item);
                });
                $data['responsibility'] = array_values($data['responsibility']); // إعادة ترقيم المصفوفة
                if (empty($data['responsibility'])) {
                    $data['responsibility'] = null;
                }
            } else {
                $data['responsibility'] = null;
            }
            
            // تنظيف البيانات الفارغة للأشخاص الموثقين
            if (isset($data['authorized_persons']) && is_array($data['authorized_persons'])) {
                $data['authorized_persons'] = array_filter($data['authorized_persons'], function($person) {
                    return !empty($person['name']) && !empty($person['phone']);
                });
                $data['authorized_persons'] = array_values($data['authorized_persons']); // إعادة ترقيم المصفوفة
            }
            
            // تنظيف البيانات الفارغة للحسابات
            if (isset($data['project_accounts']) && is_array($data['project_accounts'])) {
                $data['project_accounts'] = array_filter($data['project_accounts'], function($account) {
                    return !empty($account['username']) && !empty($account['password']) && !empty($account['url']);
                });
                // إضافة added_at لكل حساب
                foreach ($data['project_accounts'] as &$account) {
                    if (!isset($account['added_at'])) {
                        $account['added_at'] = now()->toISOString();
                    }
                }
                unset($account);
                $data['project_accounts'] = array_values($data['project_accounts']); // إعادة ترقيم المصفوفة
            }

            // إنشاء المشروع
            $project = Project::create($data);

            // ربط الموظفين بالمشروع مع صلاحيات المدير
            if ($request->has('employee_ids')) {
                $employeeIds = $request->employee_ids ?? [];
                $managerIds = $request->manager_ids ?? [];
                
                // إعداد البيانات مع الصلاحيات
                $attachData = [];
                foreach ($employeeIds as $employeeId) {
                    $attachData[$employeeId] = [
                        'role' => in_array($employeeId, $managerIds) ? 'manager' : 'member'
                    ];
                }
                
                $project->employees()->attach($attachData);
            }

            // رفع الملفات إن وجدت
            if ($request->hasFile('files')) {
                $files = $request->file('files');
                $descriptions = $request->input('file_descriptions', []);

                foreach ($files as $index => $file) {
                    if ($file->isValid()) {
                        $fileName = $file->getClientOriginalName();
                        $filePath = $file->store('project_files/' . $project->id, 'public');
                        $fileType = $file->getClientMimeType();
                        $fileSize = $file->getSize();
                        $description = $descriptions[$index] ?? null;

                        ProjectFile::create([
                            'project_id' => $project->id,
                            'file_name' => $fileName,
                            'file_path' => $filePath,
                            'file_type' => $fileType,
                            'file_size' => $fileSize,
                            'description' => $description,
                        ]);
                    }
                }
            }

            return redirect()->route('projects.index')
                ->with('success', 'تم إضافة المشروع بنجاح');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء إضافة المشروع: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Project $project)
    {
        if ($project->organization_id !== $request->user()->organization_id) {
            abort(403);
        }

        $project->load(['client', 'files', 'employees']);
        return view('projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Project $project)
    {
        if ($project->organization_id !== $request->user()->organization_id) {
            abort(403);
        }

        $organizationId = $request->user()->organization_id;
        $clients = Client::where('organization_id', $organizationId)->where('status', 'active')->get();
        $employees = Employee::where('organization_id', $organizationId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
        
        // تحميل الموظفين المرتبطين بالمشروع
        $project->load('employees');
        
        return view('projects.edit', compact('project', 'clients', 'employees'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        if ($project->organization_id !== $request->user()->organization_id) {
            abort(403);
        }

        try {
            $request->validate([
                'client_id' => 'required|exists:clients,id',
                'business_name' => 'required|string|max:255',
                'business_description' => 'nullable|string',
                'website_url' => 'nullable|url',
                'facebook_url' => 'nullable|url',
                'instagram_url' => 'nullable|url',
                'twitter_url' => 'nullable|url',
                'linkedin_url' => 'nullable|url',
                'youtube_url' => 'nullable|url',
                'tiktok_url' => 'nullable|url',
                'status' => 'required|in:active,inactive,pending',
                'responsibility' => 'nullable|array',
                'responsibility.*' => 'in:full_management,media_buyer,account_manager,design',
                'authorized_persons' => 'nullable|array',
                'authorized_persons.*.name' => 'nullable|string|max:255',
                'authorized_persons.*.phone' => 'nullable|string|max:20',
                'project_accounts' => 'nullable|array',
                'project_accounts.*.username' => 'nullable|string|max:255',
                'project_accounts.*.password' => 'nullable|string|max:255',
                'project_accounts.*.url' => 'nullable|string|max:500',
                'employee_ids' => 'nullable|array',
                'employee_ids.*' => 'exists:employees,id',
                'manager_ids' => 'nullable|array',
                'manager_ids.*' => 'exists:employees,id'
            ]);

            // تحضير البيانات للـ JSON fields
            $data = $request->all();
            
            // تنظيف البيانات الفارغة للمسؤولية
            if (isset($data['responsibility']) && is_array($data['responsibility'])) {
                $data['responsibility'] = array_filter($data['responsibility'], function($item) {
                    return !empty($item);
                });
                $data['responsibility'] = array_values($data['responsibility']); // إعادة ترقيم المصفوفة
                if (empty($data['responsibility'])) {
                    $data['responsibility'] = null;
                }
            } else {
                $data['responsibility'] = null;
            }
            
            // تنظيف البيانات الفارغة للأشخاص الموثقين
            if (isset($data['authorized_persons']) && is_array($data['authorized_persons'])) {
                $data['authorized_persons'] = array_filter($data['authorized_persons'], function($person) {
                    return !empty($person['name']) && !empty($person['phone']);
                });
                $data['authorized_persons'] = array_values($data['authorized_persons']); // إعادة ترقيم المصفوفة
            }
            
            // تنظيف البيانات الفارغة للحسابات
            if (isset($data['project_accounts']) && is_array($data['project_accounts'])) {
                $data['project_accounts'] = array_filter($data['project_accounts'], function($account) {
                    return !empty($account['username']) && !empty($account['password']) && !empty($account['url']);
                });
                // إضافة added_at لكل حساب
                foreach ($data['project_accounts'] as &$account) {
                    if (!isset($account['added_at'])) {
                        $account['added_at'] = now()->toISOString();
                    }
                }
                unset($account);
                $data['project_accounts'] = array_values($data['project_accounts']); // إعادة ترقيم المصفوفة
            }

            $project->update($data);

            // تحديث الموظفين المرتبطين بالمشروع مع صلاحيات المدير
            if ($request->has('employee_ids')) {
                $employeeIds = $request->employee_ids ?? [];
                $managerIds = $request->manager_ids ?? [];
                
                // إعداد البيانات مع الصلاحيات
                $syncData = [];
                foreach ($employeeIds as $employeeId) {
                    $syncData[$employeeId] = [
                        'role' => in_array($employeeId, $managerIds) ? 'manager' : 'member'
                    ];
                }
                
                $project->employees()->sync($syncData);
            } else {
                $project->employees()->detach();
            }

            return redirect()->route('projects.index')
                ->with('success', 'تم تحديث المشروع بنجاح');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تحديث المشروع: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Project $project)
    {
        if ($project->organization_id !== $request->user()->organization_id) {
            abort(403);
        }

        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'تم حذف المشروع بنجاح');
    }

    /**
     * Upload a file to the project.
     */
    public function uploadFile(Request $request, Project $project): RedirectResponse
    {
        if ($project->organization_id !== $request->user()->organization_id) {
            abort(403);
        }

        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'description' => 'nullable|string|max:500',
        ]);

        try {
            $file = $request->file('file');
            $fileName = $file->getClientOriginalName();
            $filePath = $file->store('project_files/' . $project->id, 'public');
            $fileType = $file->getClientMimeType();
            $fileSize = $file->getSize();

            ProjectFile::create([
                'project_id' => $project->id,
                'file_name' => $fileName,
                'file_path' => $filePath,
                'file_type' => $fileType,
                'file_size' => $fileSize,
                'description' => $request->description,
            ]);

            return redirect()->route('projects.show', $project)
                ->with('success', 'تم رفع الملف بنجاح');
        } catch (\Exception $e) {
            return redirect()->route('projects.show', $project)
                ->with('error', 'حدث خطأ أثناء رفع الملف: ' . $e->getMessage());
        }
    }

    /**
     * Download a project file.
     */
    public function downloadFile(Request $request, Project $project, ProjectFile $file)
    {
        if ($project->organization_id !== $request->user()->organization_id) {
            abort(403);
        }

        if ($file->project_id !== $project->id) {
            abort(404);
        }

        if (!Storage::disk('public')->exists($file->file_path)) {
            abort(404, 'الملف غير موجود');
        }

        return Storage::disk('public')->download($file->file_path, $file->file_name);
    }

    /**
     * Delete a project file.
     */
    public function deleteFile(Request $request, Project $project, ProjectFile $file): RedirectResponse
    {
        if ($project->organization_id !== $request->user()->organization_id) {
            abort(403);
        }

        if ($file->project_id !== $project->id) {
            abort(404);
        }

        try {
            // حذف الملف من التخزين
            if (Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }

            // حذف السجل من قاعدة البيانات
            $file->delete();

            return redirect()->route('projects.show', $project)
                ->with('success', 'تم حذف الملف بنجاح');
        } catch (\Exception $e) {
            return redirect()->route('projects.show', $project)
                ->with('error', 'حدث خطأ أثناء حذف الملف: ' . $e->getMessage());
        }
    }

    /**
     * عرض التقرير المالي الشهري للمشروع
     */
    public function financialReport(Request $request, Project $project)
    {
        if ($project->organization_id !== $request->user()->organization_id) {
            abort(403);
        }

        // الحصول على الشهر المحدد أو الشهر الحالي
        $selectedMonth = $request->get('month', Carbon::now()->format('Y-m'));
        
        try {
            $monthDate = Carbon::createFromFormat('Y-m', $selectedMonth);
        } catch (\Exception $e) {
            $monthDate = Carbon::now();
            $selectedMonth = $monthDate->format('Y-m');
        }

        $startOfMonth = $monthDate->copy()->startOfMonth();
        $endOfMonth = $monthDate->copy()->endOfMonth();

        // جلب الإيرادات في الشهر المحدد
        $revenues = ProjectRevenue::where('project_id', $project->id)
            ->whereBetween('revenue_date', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
            ->orderBy('revenue_date', 'desc')
            ->get();

        // جلب المصروفات في الشهر المحدد
        $expenses = ProjectExpense::where('project_id', $project->id)
            ->whereBetween('expense_date', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
            ->orderBy('expense_date', 'desc')
            ->get();

        // حساب الإجماليات
        $total_revenues = $revenues->sum('amount') ?? 0;
        $total_paid_revenues = $revenues->sum('paid_amount') ?? 0;
        $total_remaining_revenues = $revenues->sum(function($revenue) {
            return $revenue->calculated_remaining_amount;
        }) ?? 0;

        $total_expenses = $expenses->sum('amount') ?? 0;
        $total_paid_expenses = $expenses->where('status', 'paid')->sum('amount') ?? 0;
        $total_pending_expenses = $expenses->where('status', 'pending')->sum('amount') ?? 0;

        $net_profit = $total_paid_revenues - $total_paid_expenses;

        // إحصائيات الإيرادات حسب الحالة
        $revenues_received = $revenues->where('status', 'received')->sum('amount') ?? 0;
        $revenues_pending = $revenues->where('status', 'pending')->sum('amount') ?? 0;
        $revenues_cancelled = $revenues->where('status', 'cancelled')->sum('amount') ?? 0;

        // إحصائيات المصروفات حسب الحالة
        $expenses_paid = $expenses->where('status', 'paid')->sum('amount') ?? 0;
        $expenses_pending = $expenses->where('status', 'pending')->sum('amount') ?? 0;
        $expenses_cancelled = $expenses->where('status', 'cancelled')->sum('amount') ?? 0;

        $project->load('client');

        return view('projects.financial-report', compact(
            'project',
            'selectedMonth',
            'monthDate',
            'revenues',
            'expenses',
            'total_revenues',
            'total_paid_revenues',
            'total_remaining_revenues',
            'total_expenses',
            'total_paid_expenses',
            'total_pending_expenses',
            'net_profit',
            'revenues_received',
            'revenues_pending',
            'revenues_cancelled',
            'expenses_paid',
            'expenses_pending',
            'expenses_cancelled'
        ));
    }
}
