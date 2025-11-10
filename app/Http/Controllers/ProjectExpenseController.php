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
        
        $expenses = $expensesQuery->latest('expense_date')->get();
        
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
                'employee_id' => 'nullable|exists:employees,id',
                'category' => 'required|in:marketing,advertising,design,development,content,tools,subscriptions,other',
                'payment_method' => 'required|in:cash,bank_transfer,credit_card,check,vodafone_cash,instapay,other',
                'payment_reference' => 'nullable|string|max:255',
                'status' => 'required|in:pending,paid,cancelled',
                'notes' => 'nullable|string'
            ]);

            $project->expenses()->create($request->all());

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
                'employee_id' => 'nullable|exists:employees,id',
                'category' => 'required|in:marketing,advertising,design,development,content,tools,subscriptions,other',
                'payment_method' => 'required|in:cash,bank_transfer,credit_card,check,vodafone_cash,instapay,other',
                'payment_reference' => 'nullable|string|max:255',
                'status' => 'required|in:pending,paid,cancelled',
                'notes' => 'nullable|string'
            ]);

            $expense->update($request->all());

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
}