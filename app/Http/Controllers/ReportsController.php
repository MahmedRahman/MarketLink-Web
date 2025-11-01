<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectRevenue;
use App\Models\ProjectExpense;
use App\Models\Client;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        // Get the current user's organization ID
        $organizationId = $request->user()->organization_id;

        // Get filter parameters
        $projectId = $request->get('project_id');
        $clientId = $request->get('client_id');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $status = $request->get('status');

        // Get all projects and clients for filters (filtered by organization)
        $projects = Project::where('organization_id', $organizationId)->with('client')->get();
        $clients = Client::where('organization_id', $organizationId)->where('status', 'active')->get();

        // Build query for revenues (filtered by organization through projects)
        $revenuesQuery = ProjectRevenue::with(['project.client'])
            ->whereHas('project', function($query) use ($organizationId) {
                $query->where('organization_id', $organizationId);
            });
        $expensesQuery = ProjectExpense::with(['project.client'])
            ->whereHas('project', function($query) use ($organizationId) {
                $query->where('organization_id', $organizationId);
            });

        // Apply filters
        if ($projectId) {
            // Verify project belongs to the organization
            $projectExists = Project::where('id', $projectId)
                ->where('organization_id', $organizationId)
                ->exists();
            
            if ($projectExists) {
                $revenuesQuery->where('project_id', $projectId);
                $expensesQuery->where('project_id', $projectId);
            }
        }

        if ($clientId) {
            $revenuesQuery->whereHas('project', function($query) use ($clientId, $organizationId) {
                $query->where('client_id', $clientId)
                      ->where('organization_id', $organizationId);
            });
            $expensesQuery->whereHas('project', function($query) use ($clientId, $organizationId) {
                $query->where('client_id', $clientId)
                      ->where('organization_id', $organizationId);
            });
        }

        if ($dateFrom) {
            $revenuesQuery->where('revenue_date', '>=', $dateFrom);
            $expensesQuery->where('expense_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $revenuesQuery->where('revenue_date', '<=', $dateTo);
            $expensesQuery->where('expense_date', '<=', $dateTo);
        }

        if ($status) {
            if ($status === 'revenue') {
                $revenuesQuery->where('status', 'received');
            } elseif ($status === 'expense') {
                $expensesQuery->where('status', 'paid');
            }
        }

        // Get filtered data
        $revenues = $revenuesQuery->orderBy('revenue_date', 'desc')->get();
        $expenses = $expensesQuery->orderBy('expense_date', 'desc')->get();

        // Calculate totals
        $totalRevenues = $revenues->sum('amount');
        $totalExpenses = $expenses->sum('amount');
        $netProfit = $totalRevenues - $totalExpenses;

        // Get project statistics
        $projectStats = [];
        if ($projectId) {
            $project = Project::where('organization_id', $organizationId)
                ->with('client')
                ->find($projectId);
            
            if ($project) {
                $projectStats = [
                    'project' => $project,
                    'revenues' => $revenues,
                    'expenses' => $expenses,
                    'total_revenues' => $revenues->sum('amount'),
                    'total_expenses' => $expenses->sum('amount'),
                    'net_profit' => $revenues->sum('amount') - $expenses->sum('amount')
                ];
            }
        }

        // Get monthly data for charts
        $monthlyData = $this->getMonthlyData($revenues, $expenses);

        return view('reports.index', compact(
            'revenues',
            'expenses',
            'projects',
            'clients',
            'totalRevenues',
            'totalExpenses',
            'netProfit',
            'projectStats',
            'monthlyData',
            'projectId',
            'clientId',
            'dateFrom',
            'dateTo',
            'status'
        ));
    }

    private function getMonthlyData($revenues, $expenses)
    {
        $months = [];
        $currentMonth = Carbon::now()->startOfMonth();
        
        for ($i = 0; $i < 12; $i++) {
            $month = $currentMonth->copy()->subMonths($i);
            $monthKey = $month->format('Y-m');
            $monthName = $month->format('M Y');
            
            $monthRevenues = $revenues->filter(function($revenue) use ($month) {
                return $revenue->revenue_date->format('Y-m') === $month->format('Y-m');
            })->sum('amount');
            
            $monthExpenses = $expenses->filter(function($expense) use ($month) {
                return $expense->expense_date->format('Y-m') === $month->format('Y-m');
            })->sum('amount');
            
            $months[] = [
                'month' => $monthName,
                'revenues' => $monthRevenues,
                'expenses' => $monthExpenses,
                'profit' => $monthRevenues - $monthExpenses
            ];
        }
        
        return array_reverse($months);
    }

    public function export(Request $request)
    {
        // Get the current user's organization ID
        $organizationId = $request->user()->organization_id;

        // Get the same filtered data as index
        $projectId = $request->get('project_id');
        $clientId = $request->get('client_id');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $status = $request->get('status');

        // Build queries (same logic as index method - filtered by organization)
        $revenuesQuery = ProjectRevenue::with(['project.client'])
            ->whereHas('project', function($query) use ($organizationId) {
                $query->where('organization_id', $organizationId);
            });
        $expensesQuery = ProjectExpense::with(['project.client'])
            ->whereHas('project', function($query) use ($organizationId) {
                $query->where('organization_id', $organizationId);
            });

        if ($projectId) {
            // Verify project belongs to the organization
            $projectExists = Project::where('id', $projectId)
                ->where('organization_id', $organizationId)
                ->exists();
            
            if ($projectExists) {
                $revenuesQuery->where('project_id', $projectId);
                $expensesQuery->where('project_id', $projectId);
            }
        }

        if ($clientId) {
            $revenuesQuery->whereHas('project', function($query) use ($clientId, $organizationId) {
                $query->where('client_id', $clientId)
                      ->where('organization_id', $organizationId);
            });
            $expensesQuery->whereHas('project', function($query) use ($clientId, $organizationId) {
                $query->where('client_id', $clientId)
                      ->where('organization_id', $organizationId);
            });
        }

        if ($dateFrom) {
            $revenuesQuery->where('revenue_date', '>=', $dateFrom);
            $expensesQuery->where('expense_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $revenuesQuery->where('revenue_date', '<=', $dateTo);
            $expensesQuery->where('expense_date', '<=', $dateTo);
        }

        if ($status) {
            if ($status === 'revenue') {
                $revenuesQuery->where('status', 'received');
            } elseif ($status === 'expense') {
                $expensesQuery->where('status', 'paid');
            }
        }

        $revenues = $revenuesQuery->orderBy('revenue_date', 'desc')->get();
        $expenses = $expensesQuery->orderBy('expense_date', 'desc')->get();

        // Generate CSV content
        $csvContent = "التقارير المالية\n\n";
        $csvContent .= "الإيرادات\n";
        $csvContent .= "المشروع,العميل,العنوان,المبلغ,التاريخ,الحالة\n";
        
        foreach ($revenues as $revenue) {
            $csvContent .= sprintf(
                "%s,%s,%s,%.2f,%s,%s\n",
                $revenue->project->business_name ?? '',
                $revenue->project->client->name ?? '',
                $revenue->title,
                $revenue->amount,
                $revenue->revenue_date->format('Y-m-d'),
                $revenue->status_badge
            );
        }
        
        $csvContent .= "\nالمصروفات\n";
        $csvContent .= "المشروع,العميل,العنوان,المبلغ,التاريخ,الحالة\n";
        
        foreach ($expenses as $expense) {
            $csvContent .= sprintf(
                "%s,%s,%s,%.2f,%s,%s\n",
                $expense->project->business_name ?? '',
                $expense->project->client->name ?? '',
                $expense->title,
                $expense->amount,
                $expense->expense_date->format('Y-m-d'),
                $expense->status_badge
            );
        }

        $filename = 'reports_' . date('Y-m-d_H-i-s') . '.csv';
        
        return response($csvContent)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * تقرير الوضع المالي للموظف
     */
    public function employeeFinancial(Request $request)
    {
        // Get the current user's organization ID
        $organizationId = $request->user()->organization_id;

        // Get filter parameters
        $employeeId = $request->get('employee_id');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        // Get all employees for filter
        $employees = Employee::where('organization_id', $organizationId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $employee = null;
        $expenses = collect();
        $totalAmount = 0;
        $totalPaid = 0;
        $totalPending = 0;
        $totalCancelled = 0;

        if ($employeeId) {
            // Verify employee belongs to the organization
            $employee = Employee::where('id', $employeeId)
                ->where('organization_id', $organizationId)
                ->first();

            if ($employee) {
                // Build query for employee expenses
                $expensesQuery = ProjectExpense::where('employee_id', $employeeId)
                    ->with(['project'])
                    ->whereHas('project', function($query) use ($organizationId) {
                        $query->where('organization_id', $organizationId);
                    });

                // Apply date filters
                if ($dateFrom) {
                    $expensesQuery->where('expense_date', '>=', $dateFrom);
                }

                if ($dateTo) {
                    $expensesQuery->where('expense_date', '<=', $dateTo);
                }

                $expenses = $expensesQuery->orderBy('expense_date', 'desc')->get();

                // Calculate totals by status
                $totalAmount = $expenses->sum('amount');
                $totalPaid = $expenses->where('status', 'paid')->sum('amount');
                $totalPending = $expenses->where('status', 'pending')->sum('amount');
                $totalCancelled = $expenses->where('status', 'cancelled')->sum('amount');
            }
        }

        return view('reports.employee-financial', compact(
            'employees',
            'employee',
            'expenses',
            'totalAmount',
            'totalPaid',
            'totalPending',
            'totalCancelled',
            'employeeId',
            'dateFrom',
            'dateTo'
        ));
    }
}