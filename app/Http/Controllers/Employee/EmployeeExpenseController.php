<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeExpenseController extends Controller
{
    /**
     * Display a listing of the employee's expenses.
     */
    public function index()
    {
        $employee = Auth::guard('employee')->user();
        
        $expenses = $employee->expenses()
            ->with(['project.client'])
            ->orderBy('expense_date', 'desc')
            ->paginate(15);

        // إحصائيات
        $stats = [
            'total' => $employee->expenses()->sum('amount'),
            'paid' => $employee->expenses()->where('status', 'paid')->sum('amount'),
            'pending' => $employee->expenses()->where('status', 'pending')->sum('amount'),
            'count' => $employee->expenses()->count(),
        ];

        return view('employee.expenses.index', compact('expenses', 'stats'));
    }

    /**
     * Display the specified expense.
     */
    public function show($expenseId)
    {
        $employee = Auth::guard('employee')->user();
        
        $expense = $employee->expenses()
            ->with(['project.client'])
            ->findOrFail($expenseId);

        return view('employee.expenses.show', compact('expense'));
    }
}
