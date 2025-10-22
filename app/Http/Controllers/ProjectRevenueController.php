<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectRevenue;
use Illuminate\Http\Request;

class ProjectRevenueController extends Controller
{
    public function index(Project $project)
    {
        $revenues = $project->revenues()->latest()->paginate(10);
        return view('projects.revenues.index', compact('project', 'revenues'));
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
                'currency' => 'required|string|max:3|in:EGP',
                'revenue_date' => 'required|date',
                'payment_method' => 'required|in:cash,bank_transfer,credit_card,check,other',
                'payment_reference' => 'nullable|string|max:255',
                'status' => 'required|in:pending,received,cancelled',
                'invoice_number' => 'nullable|string|max:255',
                'invoice_date' => 'nullable|date',
                'notes' => 'nullable|string'
            ]);

            $project->revenues()->create($request->all());

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
                'currency' => 'required|string|max:3|in:EGP',
                'revenue_date' => 'required|date',
                'payment_method' => 'required|in:cash,bank_transfer,credit_card,check,other',
                'payment_reference' => 'nullable|string|max:255',
                'status' => 'required|in:pending,received,cancelled',
                'invoice_number' => 'nullable|string|max:255',
                'invoice_date' => 'nullable|date',
                'notes' => 'nullable|string'
            ]);

            $revenue->update($request->all());

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
}