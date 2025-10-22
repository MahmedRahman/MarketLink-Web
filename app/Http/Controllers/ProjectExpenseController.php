<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectExpense;
use Illuminate\Http\Request;

class ProjectExpenseController extends Controller
{
    public function index(Project $project)
    {
        $expenses = $project->expenses()->latest()->paginate(10);
        return view('projects.expenses.index', compact('project', 'expenses'));
    }

    public function create(Project $project)
    {
        return view('projects.expenses.create', compact('project'));
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
                'category' => 'required|in:marketing,advertising,design,development,content,tools,subscriptions,other',
                'payment_method' => 'required|in:cash,bank_transfer,credit_card,check,other',
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
        return view('projects.expenses.show', compact('project', 'expense'));
    }

    public function edit(Project $project, ProjectExpense $expense)
    {
        return view('projects.expenses.edit', compact('project', 'expense'));
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
                'category' => 'required|in:marketing,advertising,design,development,content,tools,subscriptions,other',
                'payment_method' => 'required|in:cash,bank_transfer,credit_card,check,other',
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
}