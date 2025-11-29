<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectExpense;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ProjectExpenseController extends Controller
{
    public function index(Request $request, Project $project)
    {
        $selectedMonth = $request->query('month');
        
        $expensesQuery = $project->expenses()->with('employee');
        
        // تطبيق فلتر الشهر إن وجد
        if ($selectedMonth) {
            try {
                $monthDate = Carbon::createFromFormat('Y-m', $selectedMonth);
                $startOfMonth = $monthDate->copy()->startOfMonth();
                $endOfMonth = $monthDate->copy()->endOfMonth();
                
                $expensesQuery->whereBetween('expense_date', [
                    $startOfMonth->toDateString(),
                    $endOfMonth->toDateString()
                ]);
            } catch (\Exception $e) {
                // في حالة خطأ في التاريخ، نستمر بدون فلتر
                \Log::warning('Error filtering expenses by month: ' . $e->getMessage());
            }
        }
        
        $expenses = $expensesQuery->latest('id')->get();
        
        return view('projects.expenses.index', compact('project', 'expenses', 'selectedMonth'));
    }

    public function create(Project $project, Request $request)
    {
        $organizationId = $request->user()->organization_id;
        $employees = Employee::where('organization_id', $organizationId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
        
        return view('projects.expenses.create', compact('project', 'employees'));
    }

    public function store(Request $request, Project $project)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'amount' => 'required|numeric|min:0',
                'currency' => 'required|string|max:3|in:EGP',
                'expense_date' => 'required|date',
                'record_month_year' => 'nullable|string|max:7|regex:/^\d{4}-\d{2}$/',
                'employee_id' => 'nullable|exists:employees,id',
                'category' => 'required|in:marketing,advertising,design,development,content,tools,subscriptions,other',
                'payment_method' => 'required|in:cash,bank_transfer,credit_card,check,vodafone_cash,instapay,other',
                'payment_reference' => 'nullable|string|max:255',
                'status' => 'required|in:pending,paid,cancelled',
                'notes' => 'nullable|string'
            ]);

            $data = $request->all();
            
            // إذا لم يتم تحديد record_month_year، استخرجه من expense_date
            if (empty($data['record_month_year']) && !empty($data['expense_date'])) {
                $data['record_month_year'] = Carbon::parse($data['expense_date'])->format('Y-m');
            }

            $project->expenses()->create($data);

            return redirect()->route('projects.expenses.index', $project)
                ->with('success', 'تم إضافة المصروف بنجاح');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء إضافة المصروف: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Project $project, ProjectExpense $expense)
    {
        $expense->load('employee');
        return view('projects.expenses.show', compact('project', 'expense'));
    }

    public function edit(Project $project, ProjectExpense $expense, Request $request)
    {
        $organizationId = $request->user()->organization_id;
        $employees = Employee::where('organization_id', $organizationId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
        
        return view('projects.expenses.edit', compact('project', 'expense', 'employees'));
    }

    public function update(Request $request, Project $project, ProjectExpense $expense)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'amount' => 'required|numeric|min:0',
                'currency' => 'required|string|max:3|in:EGP',
                'expense_date' => 'required|date',
                'record_month_year' => 'nullable|string|max:7|regex:/^\d{4}-\d{2}$/',
                'employee_id' => 'nullable|exists:employees,id',
                'category' => 'required|in:marketing,advertising,design,development,content,tools,subscriptions,other',
                'payment_method' => 'required|in:cash,bank_transfer,credit_card,check,vodafone_cash,instapay,other',
                'payment_reference' => 'nullable|string|max:255',
                'status' => 'required|in:pending,paid,cancelled',
                'notes' => 'nullable|string'
            ]);

            $data = $request->all();
            
            // إذا لم يتم تحديد record_month_year، استخرجه من expense_date
            if (empty($data['record_month_year']) && !empty($data['expense_date'])) {
                $data['record_month_year'] = Carbon::parse($data['expense_date'])->format('Y-m');
            }

            $expense->update($data);

            return redirect()->route('projects.expenses.index', $project)
                ->with('success', 'تم تحديث المصروف بنجاح');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تحديث المصروف: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Project $project, ProjectExpense $expense)
    {
        try {
            $expense->delete();
            return redirect()->route('projects.expenses.index', $project)
                ->with('success', 'تم حذف المصروف بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء حذف المصروف: ' . $e->getMessage());
        }
    }

    /**
     * Duplicate an existing expense
     */
    public function duplicate(Project $project, ProjectExpense $expense)
    {
        try {
            // Create a new expense with the same data (except id and timestamps)
            $newExpense = $expense->replicate();
            $newExpense->title = $expense->title . ' (نسخة)';
            $newExpense->status = 'pending'; // Set status to pending for duplicate
            $newExpense->save();

            return redirect()->route('projects.expenses.index', $project)
                ->with('success', 'تم نسخ المصروف بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء نسخ المصروف: ' . $e->getMessage());
        }
    }

    /**
     * Duplicate multiple expenses with new record_month_year
     */
    public function bulkDuplicate(Request $request, Project $project)
    {
        try {
            $request->validate([
                'expense_ids' => 'required|array|min:1',
                'expense_ids.*' => 'exists:project_expenses,id',
                'record_month_year' => 'required|string|max:7|regex:/^\d{4}-\d{2}$/',
            ]);

            $expenseIds = $request->input('expense_ids');
            $recordMonthYear = $request->input('record_month_year');
            
            // التحقق من أن جميع المصروفات تتبع المشروع
            $expenses = ProjectExpense::where('project_id', $project->id)
                ->whereIn('id', $expenseIds)
                ->get();

            if ($expenses->count() !== count($expenseIds)) {
                return redirect()->back()
                    ->with('error', 'بعض المصروفات المحددة غير موجودة أو لا تنتمي لهذا المشروع');
            }

            $duplicatedCount = 0;
            foreach ($expenses as $expense) {
                $newExpense = $expense->replicate();
                $newExpense->title = $expense->title . ' (نسخة)';
                $newExpense->status = 'pending';
                $newExpense->record_month_year = $recordMonthYear;
                $newExpense->save();
                $duplicatedCount++;
            }

            return redirect()->route('projects.expenses.index', $project)
                ->with('success', "تم نسخ {$duplicatedCount} مصروف بنجاح مع الشهر المحاسبي: {$recordMonthYear}");
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء نسخ المصروفات: ' . $e->getMessage());
        }
    }

    /**
     * عرض جميع مصروفات المشاريع
     */
    public function all(Request $request)
    {
        $user = $request->user();
        $organizationId = $user->organization_id;

        // جلب جميع المصروفات للمشاريع التابعة للمنظمة
        $expensesQuery = ProjectExpense::with(['project', 'employee'])
            ->whereHas('project', function($query) use ($organizationId) {
                $query->where('organization_id', $organizationId);
            });

        // فلترة حسب المشروع
        if ($request->has('project_id') && $request->project_id) {
            $expensesQuery->where('project_id', $request->project_id);
        }

        // فلترة حسب الشهر (تاريخ المصروف)
        if ($request->has('month') && $request->month) {
            try {
                $monthDate = Carbon::createFromFormat('Y-m', $request->month);
                $startOfMonth = $monthDate->copy()->startOfMonth();
                $endOfMonth = $monthDate->copy()->endOfMonth();
                
                $expensesQuery->whereBetween('expense_date', [
                    $startOfMonth->toDateString(),
                    $endOfMonth->toDateString()
                ]);
            } catch (\Exception $e) {
                \Log::warning('Error filtering expenses by month: ' . $e->getMessage());
            }
        }

        // فلترة حسب السجلات الشهرية
        if ($request->has('record_month_year') && $request->record_month_year) {
            $expensesQuery->where('record_month_year', $request->record_month_year);
        }

        // فلترة حسب الحالة
        if ($request->has('status') && $request->status) {
            $expensesQuery->where('status', $request->status);
        }

        // فلترة حسب الفئة
        if ($request->has('category') && $request->category) {
            $expensesQuery->where('category', $request->category);
        }

        $expenses = $expensesQuery->latest('expense_date')->get();
        
        // جلب جميع المشاريع للمنظمة للفلترة
        $projects = Project::where('organization_id', $organizationId)
            ->orderBy('business_name')
            ->get();

        return view('projects.expenses.all', compact('expenses', 'projects'));
    }

    /**
     * عرض صفحة إضافة مصروف جديد (لجميع المشاريع)
     */
    public function createAll(Request $request)
    {
        $user = $request->user();
        $organizationId = $user->organization_id;
        
        $projects = Project::where('organization_id', $organizationId)
            ->orderBy('business_name')
            ->get();

        $employees = Employee::where('organization_id', $organizationId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('projects.expenses.create-all', compact('projects', 'employees'));
    }

    /**
     * حفظ مصروف جديد (لجميع المشاريع)
     */
    public function storeAll(Request $request)
    {
        try {
            $request->validate([
                'project_id' => 'required|exists:projects,id',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'amount' => 'required|numeric|min:0',
                'currency' => 'required|string|max:3|in:EGP',
                'expense_date' => 'required|date',
                'record_month_year' => 'nullable|string|max:7|regex:/^\d{4}-\d{2}$/',
                'employee_id' => 'nullable|exists:employees,id',
                'category' => 'required|in:marketing,advertising,design,development,content,tools,subscriptions,other',
                'payment_method' => 'required|in:cash,bank_transfer,credit_card,check,vodafone_cash,instapay,other',
                'payment_reference' => 'nullable|string|max:255',
                'status' => 'required|in:pending,paid,cancelled',
                'notes' => 'nullable|string'
            ]);

            $project = Project::findOrFail($request->project_id);
            
            // التحقق من أن المشروع تابع للمنظمة
            if ($project->organization_id !== $request->user()->organization_id) {
                abort(403);
            }

            $data = $request->all();
            
            // إذا لم يتم تحديد record_month_year، استخرجه من expense_date
            if (empty($data['record_month_year']) && !empty($data['expense_date'])) {
                $data['record_month_year'] = Carbon::parse($data['expense_date'])->format('Y-m');
            }

            $project->expenses()->create($data);

            return redirect()->route('expenses.all')
                ->with('success', 'تم إضافة المصروف بنجاح');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء إضافة المصروف: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * عرض صفحة تعديل مصروف (لجميع المشاريع)
     */
    public function editAll(Request $request, ProjectExpense $expense)
    {
        // التحقق من أن المصروف تابع لمشروع في منظمة المستخدم
        if ($expense->project->organization_id !== $request->user()->organization_id) {
            abort(403);
        }

        $user = $request->user();
        $organizationId = $user->organization_id;
        
        $projects = Project::where('organization_id', $organizationId)
            ->orderBy('business_name')
            ->get();

        $employees = Employee::where('organization_id', $organizationId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('projects.expenses.edit-all', compact('expense', 'projects', 'employees'));
    }

    /**
     * تحديث مصروف (لجميع المشاريع)
     */
    public function updateAll(Request $request, ProjectExpense $expense)
    {
        try {
            // التحقق من أن المصروف تابع لمشروع في منظمة المستخدم
            if ($expense->project->organization_id !== $request->user()->organization_id) {
                abort(403);
            }

            $request->validate([
                'project_id' => 'required|exists:projects,id',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'amount' => 'required|numeric|min:0',
                'currency' => 'required|string|max:3|in:EGP',
                'expense_date' => 'required|date',
                'record_month_year' => 'nullable|string|max:7|regex:/^\d{4}-\d{2}$/',
                'employee_id' => 'nullable|exists:employees,id',
                'category' => 'required|in:marketing,advertising,design,development,content,tools,subscriptions,other',
                'payment_method' => 'required|in:cash,bank_transfer,credit_card,check,vodafone_cash,instapay,other',
                'payment_reference' => 'nullable|string|max:255',
                'status' => 'required|in:pending,paid,cancelled',
                'notes' => 'nullable|string'
            ]);

            $project = Project::findOrFail($request->project_id);
            
            // التحقق من أن المشروع تابع للمنظمة
            if ($project->organization_id !== $request->user()->organization_id) {
                abort(403);
            }

            $data = $request->all();
            
            // إذا لم يتم تحديد record_month_year، استخرجه من expense_date
            if (empty($data['record_month_year']) && !empty($data['expense_date'])) {
                $data['record_month_year'] = Carbon::parse($data['expense_date'])->format('Y-m');
            }

            $expense->update($data);

            return redirect()->route('expenses.all')
                ->with('success', 'تم تحديث المصروف بنجاح');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تحديث المصروف: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * حذف مصروف (لجميع المشاريع)
     */
    public function destroyAll(Request $request, ProjectExpense $expense)
    {
        try {
            // التحقق من أن المصروف تابع لمشروع في منظمة المستخدم
            if ($expense->project->organization_id !== $request->user()->organization_id) {
                abort(403);
            }

            $expense->delete();
            return redirect()->route('expenses.all')
                ->with('success', 'تم حذف المصروف بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء حذف المصروف: ' . $e->getMessage());
        }
    }

    /**
     * نسخ مصروف (لجميع المشاريع)
     */
    public function duplicateAll(Request $request, ProjectExpense $expense)
    {
        try {
            // التحقق من أن المصروف تابع لمشروع في منظمة المستخدم
            if ($expense->project->organization_id !== $request->user()->organization_id) {
                abort(403);
            }

            $newExpense = $expense->replicate();
            $newExpense->title = $expense->title . ' (نسخة)';
            $newExpense->status = 'pending';
            $newExpense->save();

            return redirect()->route('expenses.all')
                ->with('success', 'تم نسخ المصروف بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء نسخ المصروف: ' . $e->getMessage());
        }
    }
}