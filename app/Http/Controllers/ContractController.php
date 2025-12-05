<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Employee;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $organizationId = $request->user()->organization_id;
        $contracts = Contract::where('organization_id', $organizationId)
            ->with('employee')
            ->orderBy('start_date', 'desc')
            ->paginate(15);

        // حساب الإحصائيات
        $allContracts = Contract::where('organization_id', $organizationId)->get();
        
        $stats = [
            'total_contracts' => $allContracts->count(),
            'total_amount' => $allContracts->sum('agreed_amount'),
            'active_contracts' => $allContracts->where('status', 'active')->count(),
            'active_amount' => $allContracts->where('status', 'active')->sum('agreed_amount'),
        ];
        
        return view('contracts.index', compact('contracts', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $organizationId = $request->user()->organization_id;
        $employees = Employee::where('organization_id', $organizationId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
        
        return view('contracts.create', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'payment_type' => 'required|in:hourly,project,piece',
            'agreed_amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,completed,cancelled'
        ]);

        $data = $request->all();
        $data['organization_id'] = $request->user()->organization_id;

        Contract::create($data);

        return redirect()->route('contracts.index')
            ->with('success', 'تم إضافة العقد بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Contract $contract)
    {
        if ($contract->organization_id !== $request->user()->organization_id) {
            abort(403);
        }

        $contract->load(['employee', 'organization']);

        return view('contracts.show', compact('contract'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Contract $contract)
    {
        if ($contract->organization_id !== $request->user()->organization_id) {
            abort(403);
        }

        $organizationId = $request->user()->organization_id;
        $employees = Employee::where('organization_id', $organizationId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('contracts.edit', compact('contract', 'employees'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contract $contract)
    {
        if ($contract->organization_id !== $request->user()->organization_id) {
            abort(403);
        }

        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'payment_type' => 'required|in:hourly,project,piece',
            'agreed_amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,completed,cancelled'
        ]);

        $contract->update($request->all());

        return redirect()->route('contracts.index')
            ->with('success', 'تم تحديث العقد بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Contract $contract)
    {
        if ($contract->organization_id !== $request->user()->organization_id) {
            abort(403);
        }

        $contract->delete();

        return redirect()->route('contracts.index')
            ->with('success', 'تم حذف العقد بنجاح');
    }
}
