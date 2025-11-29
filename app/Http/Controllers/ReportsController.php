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
        $recordMonth = $request->get('record_month');

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

                // Apply record_month_year filter
                if ($recordMonth) {
                    $expensesQuery->where('record_month_year', $recordMonth);
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
            'recordMonth'
        ));
    }

    /**
     * تقرير المديونية - عرض كل مشروع مع المبلغ المتبقي والمحصل
     */
    public function receivables(Request $request)
    {
        // Get the current user's organization ID
        $organizationId = $request->user()->organization_id;

        // Get selected month filter (for record_month_year)
        $selectedMonth = $request->query('month');

        // Get all projects with their revenues
        $projects = Project::where('organization_id', $organizationId)
            ->with(['client', 'revenues'])
            ->get();

        // Calculate totals for each project with month filter
        $projectsData = $projects->map(function($project) use ($selectedMonth) {
            $revenues = $project->revenues;
            
            // Filter revenues by record_month_year if selected
            if ($selectedMonth) {
                $revenues = $revenues->filter(function($revenue) use ($selectedMonth) {
                    return $revenue->record_month_year === $selectedMonth;
                });
            }
            
            $totalPaid = $revenues->sum('paid_amount');
            $totalRemaining = $revenues->sum(function($revenue) {
                return $revenue->calculated_remaining_amount;
            });
            
            return [
                'project' => $project,
                'total_paid' => $totalPaid,
                'total_remaining' => $totalRemaining,
            ];
        });

        // Calculate grand totals
        $grandTotalPaid = $projectsData->sum('total_paid');
        $grandTotalRemaining = $projectsData->sum('total_remaining');

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

        return view('reports.receivables', compact(
            'projectsData',
            'grandTotalPaid',
            'grandTotalRemaining',
            'selectedMonth',
            'monthlyRecords',
            'totalCount'
        ));
    }

    /**
     * تقرير الأرباح - عرض كل مشروع مع الإيرادات المحصلة والمصروفات المدفوعة والربح
     */
    public function profits(Request $request)
    {
        // Get the current user's organization ID
        $organizationId = $request->user()->organization_id;

        // Get selected month filter (for record_month_year)
        $selectedMonth = $request->query('month');

        // Get all projects with their revenues and expenses
        $projects = Project::where('organization_id', $organizationId)
            ->with(['client', 'revenues', 'expenses'])
            ->get();

        // Calculate totals for each project with month filter
        $projectsData = $projects->map(function($project) use ($selectedMonth) {
            $revenues = $project->revenues;
            $expenses = $project->expenses;
            
            // Filter revenues and expenses by record_month_year if selected
            if ($selectedMonth) {
                $revenues = $revenues->filter(function($revenue) use ($selectedMonth) {
                    return $revenue->record_month_year === $selectedMonth;
                });
                
                $expenses = $expenses->filter(function($expense) use ($selectedMonth) {
                    return $expense->record_month_year === $selectedMonth;
                });
            }
            
            $totalPaidRevenues = $revenues->sum('paid_amount');
            $totalPaidExpenses = $expenses->where('status', 'paid')->sum('amount');
            $profit = $totalPaidRevenues - $totalPaidExpenses;
            
            return [
                'project' => $project,
                'total_paid_revenues' => $totalPaidRevenues,
                'total_paid_expenses' => $totalPaidExpenses,
                'profit' => $profit,
            ];
        });

        // Calculate grand totals
        $grandTotalPaidRevenues = $projectsData->sum('total_paid_revenues');
        $grandTotalPaidExpenses = $projectsData->sum('total_paid_expenses');
        $grandTotalProfit = $projectsData->sum('profit');

        return view('reports.profits', compact(
            'projectsData',
            'grandTotalPaidRevenues',
            'grandTotalPaidExpenses',
            'grandTotalProfit',
            'selectedMonth'
        ));
    }

    /**
     * إجمالي التقرير المالي للموظفين - عرض كل موظف مع المبلغ المدفوع والمستحق
     */
    public function totalEmployeesFinancial(Request $request)
    {
        // Get the current user's organization ID
        $organizationId = $request->user()->organization_id;

        // Get filter parameters
        $recordMonth = $request->query('record_month');

        // Get all employees
        $employees = Employee::where('organization_id', $organizationId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        // Calculate totals for each employee
        $employeesData = $employees->map(function($employee) use ($organizationId, $recordMonth) {
            $expensesQuery = \App\Models\ProjectExpense::where('employee_id', $employee->id)
                ->whereHas('project', function($query) use ($organizationId) {
                    $query->where('organization_id', $organizationId);
                });

            // Apply record_month_year filter
            if ($recordMonth) {
                $expensesQuery->where('record_month_year', $recordMonth);
            }

            $expenses = $expensesQuery->get();

            $totalPaid = $expenses->where('status', 'paid')->sum('amount');
            $totalPending = $expenses->where('status', 'pending')->sum('amount');
            
            // Get projects the employee is assigned to
            $projects = $employee->projects()
                ->where('organization_id', $organizationId)
                ->with('client')
                ->get();
            
            $projectsCount = $projects->count();
            
            return [
                'employee' => $employee,
                'total_paid' => $totalPaid,
                'total_pending' => $totalPending,
                'projects_count' => $projectsCount,
                'projects' => $projects,
                'has_expenses' => ($totalPaid > 0 || $totalPending > 0),
            ];
        });

        // Calculate grand totals
        $grandTotalPaid = $employeesData->sum('total_paid');
        $grandTotalPending = $employeesData->sum('total_pending');

        // جلب جميع السجلات الشهرية الفريدة مع عدد السجلات لكل شهر
        $monthlyRecordsData = \App\Models\ProjectExpense::whereHas('project', function($query) use ($organizationId) {
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
        $totalCount = \App\Models\ProjectExpense::whereHas('project', function($query) use ($organizationId) {
            $query->where('organization_id', $organizationId);
        })->count();

        return view('reports.total-employees-financial', compact(
            'employeesData',
            'grandTotalPaid',
            'grandTotalPending',
            'recordMonth',
            'monthlyRecords',
            'totalCount'
        ));
    }

    /**
     * عرض تفاصيل المصروفات المدفوعة للموظف
     */
    public function employeePaidExpenses(Request $request, Employee $employee)
    {
        // Verify employee belongs to the organization
        $organizationId = $request->user()->organization_id;
        
        if ($employee->organization_id !== $organizationId) {
            abort(403);
        }

        // Get filter parameters
        $recordMonth = $request->query('record_month');

        // Build query for paid expenses
        $expensesQuery = \App\Models\ProjectExpense::where('employee_id', $employee->id)
            ->where('status', 'paid')
            ->with(['project'])
            ->whereHas('project', function($query) use ($organizationId) {
                $query->where('organization_id', $organizationId);
            });

        // Apply record_month_year filter
        if ($recordMonth) {
            $expensesQuery->where('record_month_year', $recordMonth);
        }

        $expenses = $expensesQuery->orderBy('expense_date', 'desc')->get();
        $totalPaid = $expenses->sum('amount');

        return view('reports.employee-paid-expenses', compact(
            'employee',
            'expenses',
            'totalPaid',
            'recordMonth'
        ));
    }

    /**
     * تقرير بيانات الموظفين - عرض بيانات الموظفين الفعلية
     */
    public function employeesData(Request $request)
    {
        // Get the current user's organization ID
        $organizationId = $request->user()->organization_id;

        // Get all employees with their projects
        $employees = Employee::where('organization_id', $organizationId)
            ->with(['projects' => function($query) use ($organizationId) {
                $query->where('organization_id', $organizationId)
                      ->select('projects.id', 'projects.business_name');
            }])
            ->orderBy('name')
            ->get();

        return view('reports.employees-data', compact('employees'));
    }
}