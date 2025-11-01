<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $organizationId = $user->organization_id;
        
        // Get organization and subscription info
        $organization = $user->organization;
        $subscription = $organization ? $organization->activeSubscription() : null;
        $isOnTrial = $user->organizationIsOnTrial();
        $trialExpired = $user->organizationTrialExpired();
        $hasActiveSubscription = $user->organizationHasActiveSubscription();
        
        // Get statistics for the organization
        $stats = [
            'total_clients' => Client::where('organization_id', $organizationId)->count(),
            'total_projects' => Project::where('organization_id', $organizationId)->count(),
            'active_projects' => Project::where('organization_id', $organizationId)
                ->where('status', 'active')
                ->count(),
            'total_employees' => Employee::where('organization_id', $organizationId)->count(),
            'active_employees' => Employee::where('organization_id', $organizationId)
                ->where('status', 'active')
                ->count(),
        ];
        
        // Get recent clients
        $recent_clients = Client::where('organization_id', $organizationId)
            ->latest()
            ->take(5)
            ->get();
        
        // Get recent projects
        $recent_projects = Project::where('organization_id', $organizationId)
            ->with('client')
            ->latest()
            ->take(5)
            ->get();
        
        // Calculate total revenues and expenses
        $total_revenues = \App\Models\ProjectRevenue::whereHas('project', function($query) use ($organizationId) {
            $query->where('organization_id', $organizationId);
        })->sum('amount') ?? 0;
        
        $total_expenses = \App\Models\ProjectExpense::whereHas('project', function($query) use ($organizationId) {
            $query->where('organization_id', $organizationId);
        })->sum('amount') ?? 0;
        
        $stats['total_revenues'] = $total_revenues;
        $stats['total_expenses'] = $total_expenses;
        $stats['net_profit'] = $total_revenues - $total_expenses;
        
        return view('dashboard', compact(
            'organization',
            'subscription',
            'isOnTrial',
            'trialExpired',
            'hasActiveSubscription',
            'stats',
            'recent_clients',
            'recent_projects'
        ));
    }
}
