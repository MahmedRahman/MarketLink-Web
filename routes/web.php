<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectRevenueController;
use App\Http\Controllers\ProjectExpenseController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\SubscriptionRequestController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\Employee\Auth\EmployeeAuthController;
use App\Http\Controllers\Employee\EmployeeDashboardController;
use App\Http\Controllers\Employee\EmployeeTaskController;
use App\Http\Controllers\Employee\EmployeeProjectController;
use App\Http\Controllers\Employee\EmployeeExpenseController;
use App\Http\Controllers\Employee\EmployeeMonthlyPlanController;
use App\Http\Controllers\Employee\EmployeeProfileController;
use App\Http\Controllers\Api\TasksController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\ContractController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $plans = \App\Models\Plan::with('features')
        ->active()
        ->ordered()
        ->get();
    return view('welcome', compact('plans'));
})->name('welcome');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', \App\Http\Middleware\CheckTrialStatus::class])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Clients Routes
    Route::resource('clients', ClientController::class);
    
    // Meetings Routes
    Route::resource('meetings', MeetingController::class);
    
    // Contracts Routes
    Route::resource('contracts', ContractController::class);
    
    // Brand Style Extractors Routes
    Route::resource('brand-style-extractors', \App\Http\Controllers\BrandStyleExtractorController::class);
    Route::get('projects/{project}/content/create', [\App\Http\Controllers\BrandStyleExtractorController::class, 'create'])->name('projects.content.create');
    Route::post('brand-style-extractors/{brandStyleExtractor}/analyze', [\App\Http\Controllers\BrandStyleExtractorController::class, 'analyzeContent'])->name('brand-style-extractors.analyze');
    
           // Projects Routes
           Route::resource('projects', ProjectController::class);
           Route::get('projects/{project}/analyze', [ProjectController::class, 'showAnalyzePage'])->name('projects.analyze');
           Route::post('projects/{project}/analyze-content', [ProjectController::class, 'analyzeProjectContent'])->name('projects.analyze-content');
           Route::post('projects/{project}/files', [ProjectController::class, 'uploadFile'])->name('projects.files.upload');
           Route::get('projects/{project}/files/{file}/download', [ProjectController::class, 'downloadFile'])->name('projects.files.download');
           Route::delete('projects/{project}/files/{file}', [ProjectController::class, 'deleteFile'])->name('projects.files.delete');
           
           // Project Revenues Routes
           Route::resource('projects.revenues', ProjectRevenueController::class);
           Route::post('projects/{project}/revenues/{revenue}/duplicate', [ProjectRevenueController::class, 'duplicate'])->name('projects.revenues.duplicate');
           
           // All Project Revenues Routes (إيرادات المشاريع)
           Route::get('revenues', [ProjectRevenueController::class, 'all'])->name('revenues.all');
           Route::get('revenues/create', [ProjectRevenueController::class, 'createAll'])->name('revenues.create');
           Route::post('revenues', [ProjectRevenueController::class, 'storeAll'])->name('revenues.store');
           Route::get('revenues/{revenue}/edit', [ProjectRevenueController::class, 'editAll'])->name('revenues.edit');
           Route::put('revenues/{revenue}', [ProjectRevenueController::class, 'updateAll'])->name('revenues.update');
           Route::delete('revenues/{revenue}', [ProjectRevenueController::class, 'destroyAll'])->name('revenues.destroy');
           Route::post('revenues/{revenue}/duplicate', [ProjectRevenueController::class, 'duplicateAll'])->name('revenues.duplicate');
           
           // Project Expenses Routes
           Route::resource('projects.expenses', ProjectExpenseController::class);
           Route::post('projects/{project}/expenses/{expense}/duplicate', [ProjectExpenseController::class, 'duplicate'])->name('projects.expenses.duplicate');
           Route::post('projects/{project}/expenses/bulk-duplicate', [ProjectExpenseController::class, 'bulkDuplicate'])->name('projects.expenses.bulk-duplicate');
           
           // All Project Expenses Routes (مصروفات المشاريع)
           Route::get('expenses', [ProjectExpenseController::class, 'all'])->name('expenses.all');
           Route::get('expenses/create', [ProjectExpenseController::class, 'createAll'])->name('expenses.create');
           Route::post('expenses', [ProjectExpenseController::class, 'storeAll'])->name('expenses.store');
           Route::get('expenses/{expense}/edit', [ProjectExpenseController::class, 'editAll'])->name('expenses.edit');
           Route::put('expenses/{expense}', [ProjectExpenseController::class, 'updateAll'])->name('expenses.update');
           Route::delete('expenses/{expense}', [ProjectExpenseController::class, 'destroyAll'])->name('expenses.destroy');
           Route::post('expenses/{expense}/duplicate', [ProjectExpenseController::class, 'duplicateAll'])->name('expenses.duplicate');
           
           // Project Financial Report Routes
           Route::get('projects/{project}/financial-report', [ProjectController::class, 'financialReport'])->name('projects.financial-report');
           
           // Employees Routes
           Route::resource('employees', EmployeeController::class);
           
           // Monthly Plans Routes
           Route::resource('monthly-plans', \App\Http\Controllers\MonthlyPlanController::class);
           Route::post('monthly-plans/{monthlyPlan}/generate-tasks', [\App\Http\Controllers\MonthlyPlanController::class, 'generateTasks'])->name('monthly-plans.generate-tasks');
           Route::get('monthly-plans/{monthlyPlan}/tasks/create', [\App\Http\Controllers\PlanTaskController::class, 'create'])->name('monthly-plans.tasks.create');
           Route::post('monthly-plans/{monthlyPlan}/tasks', [\App\Http\Controllers\PlanTaskController::class, 'store'])->name('monthly-plans.tasks.store');
           Route::get('monthly-plans/{monthlyPlan}/tasks/{task}', [\App\Http\Controllers\PlanTaskController::class, 'show'])->name('monthly-plans.tasks.show');
           Route::get('monthly-plans/{monthlyPlan}/tasks/{task}/edit', [\App\Http\Controllers\PlanTaskController::class, 'edit'])->name('monthly-plans.tasks.edit');
           Route::put('monthly-plans/{monthlyPlan}/tasks/{task}', [\App\Http\Controllers\PlanTaskController::class, 'update'])->name('monthly-plans.tasks.update');
           Route::post('monthly-plans/{monthlyPlan}/tasks/{task}/comments', [\App\Http\Controllers\PlanTaskController::class, 'storeComment'])->name('monthly-plans.tasks.comments.store');
           Route::post('monthly-plans/{monthlyPlan}/tasks/{task}/move', [\App\Http\Controllers\PlanTaskController::class, 'move'])->name('monthly-plans.tasks.move');
           Route::post('monthly-plans/{monthlyPlan}/tasks/{task}/quick-assign', [\App\Http\Controllers\PlanTaskController::class, 'quickAssign'])->name('monthly-plans.tasks.quick-assign');
           Route::post('monthly-plans/{monthlyPlan}/tasks/bulk-assign', [\App\Http\Controllers\PlanTaskController::class, 'bulkAssign'])->name('monthly-plans.tasks.bulk-assign');
           Route::delete('monthly-plans/{monthlyPlan}/tasks/{task}', [\App\Http\Controllers\PlanTaskController::class, 'destroy'])->name('monthly-plans.tasks.destroy');
           Route::get('monthly-plans/{monthlyPlan}/tasks/{task}/files/{file}/view', [\App\Http\Controllers\PlanTaskController::class, 'viewFile'])->name('monthly-plans.tasks.files.view');
           Route::get('monthly-plans/{monthlyPlan}/tasks/{task}/files/{file}/download', [\App\Http\Controllers\PlanTaskController::class, 'downloadFile'])->name('monthly-plans.tasks.files.download');
           Route::delete('monthly-plans/{monthlyPlan}/tasks/{task}/files/{file}', [\App\Http\Controllers\PlanTaskController::class, 'deleteFile'])->name('monthly-plans.tasks.files.delete');
           
           // Generate Description Route
           Route::post('/tasks/generate-description', [\App\Http\Controllers\PlanTaskController::class, 'generateDescription'])->name('tasks.generate-description');
           Route::post('/tasks/suggest-ideas', [\App\Http\Controllers\PlanTaskController::class, 'suggestIdeas'])->name('tasks.suggest-ideas');
           Route::post('/tasks/show-prompt', [\App\Http\Controllers\PlanTaskController::class, 'showPrompt'])->name('tasks.show-prompt');
           Route::post('/tasks/suggest-post', [\App\Http\Controllers\PlanTaskController::class, 'suggestPost'])->name('tasks.suggest-post');
           Route::post('/tasks/show-post-prompt', [\App\Http\Controllers\PlanTaskController::class, 'showPostPrompt'])->name('tasks.show-post-prompt');
           Route::post('/tasks/suggest-design', [\App\Http\Controllers\PlanTaskController::class, 'suggestDesign'])->name('tasks.suggest-design');
           Route::post('/tasks/show-design-prompt', [\App\Http\Controllers\PlanTaskController::class, 'showDesignPrompt'])->name('tasks.show-design-prompt');
           Route::post('/tasks/generate-design-image', [\App\Http\Controllers\PlanTaskController::class, 'generateDesignImage'])->name('tasks.generate-design-image');
           
           // Reports Routes
           Route::get('reports', [ReportsController::class, 'index'])->name('reports.index');
           Route::get('reports/export', [ReportsController::class, 'export'])->name('reports.export');
           Route::get('reports/employee-financial', [ReportsController::class, 'employeeFinancial'])->name('reports.employee-financial');
           Route::get('reports/receivables', [ReportsController::class, 'receivables'])->name('reports.receivables');
           Route::get('reports/profits', [ReportsController::class, 'profits'])->name('reports.profits');
           Route::get('reports/total-employees-financial', [ReportsController::class, 'totalEmployeesFinancial'])->name('reports.total-employees-financial');
           Route::post('reports/total-employees-financial/{employee}/mark-paid', [ReportsController::class, 'markEmployeeExpensesAsPaid'])->name('reports.mark-employee-expenses-paid');
           Route::get('reports/employees/{employee}/paid-expenses', [ReportsController::class, 'employeePaidExpenses'])->name('reports.employee-paid-expenses');
           Route::get('reports/employees-data', [ReportsController::class, 'employeesData'])->name('reports.employees-data');
       });

// Subscription Routes
Route::middleware('auth')->group(function () {
    Route::get('/subscription', [SubscriptionController::class, 'index'])->name('subscription.index');
    Route::get('/subscription/expired', [SubscriptionController::class, 'expired'])->name('subscription.expired');
    Route::get('/subscription/plans', [SubscriptionController::class, 'plans'])->name('subscription.plans');
    Route::post('/subscription/subscribe', [SubscriptionController::class, 'subscribe'])->name('subscription.subscribe');
});

// Stop Impersonating Route (accessible when impersonating)
Route::middleware('auth')->post('/admin/stop-impersonating', [App\Http\Controllers\Admin\AdminController::class, 'stopImpersonating'])->name('admin.stop-impersonating');

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Users Routes
    Route::get('/users', [AdminController::class, 'users'])->name('users.index');
    Route::get('/users/{user}', [AdminController::class, 'showUser'])->name('users.show');
    Route::patch('/users/{user}/status', [AdminController::class, 'updateUserStatus'])->name('users.updateStatus');
    Route::patch('/users/{user}/password', [AdminController::class, 'updateUserPassword'])->name('users.updatePassword');
    Route::post('/users/{user}/impersonate', [AdminController::class, 'impersonate'])->name('users.impersonate');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.delete');
    
    // Organizations Routes
    Route::get('/organizations', [AdminController::class, 'organizations'])->name('organizations.index');
    Route::get('/organizations/{organization}', [AdminController::class, 'showOrganization'])->name('organizations.show');
    Route::delete('/organizations/{organization}', [AdminController::class, 'deleteOrganization'])->name('organizations.delete');
    
    // Subscriptions Routes
    Route::get('/subscriptions', [AdminController::class, 'subscriptions'])->name('subscriptions.index');
    Route::get('/subscriptions/{subscription}', [AdminController::class, 'showSubscription'])->name('subscriptions.show');
    Route::patch('/subscriptions/{subscription}', [AdminController::class, 'updateSubscription'])->name('subscriptions.update');
    
    // Plans Routes
    Route::resource('plans', PlanController::class);
    
    // Subscription Requests Routes
    Route::get('/subscription-requests', [SubscriptionRequestController::class, 'index'])->name('subscription-requests.index');
    Route::get('/subscription-requests/{subscriptionRequest}', [SubscriptionRequestController::class, 'show'])->name('subscription-requests.show');
    Route::post('/subscription-requests/{subscriptionRequest}/approve', [SubscriptionRequestController::class, 'approve'])->name('subscription-requests.approve');
    Route::post('/subscription-requests/{subscriptionRequest}/reject', [SubscriptionRequestController::class, 'reject'])->name('subscription-requests.reject');
});

// Employee Routes (Authentication)
Route::middleware('guest:employee')->prefix('employee')->name('employee.')->group(function () {
    Route::get('/login', [EmployeeAuthController::class, 'create'])->name('login');
    Route::post('/login', [EmployeeAuthController::class, 'store'])->name('login.store');
});

Route::middleware('auth:employee')->prefix('employee')->name('employee.')->group(function () {
    Route::get('/dashboard', [EmployeeDashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [EmployeeAuthController::class, 'destroy'])->name('logout');
    
    // Profile Routes
    Route::get('/profile', [EmployeeProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [EmployeeProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [EmployeeProfileController::class, 'updatePassword'])->name('profile.password.update');
    
    // Tasks Routes
    Route::get('/tasks/{task}', [EmployeeTaskController::class, 'show'])->name('tasks.show');
    Route::get('/tasks/{task}/edit', [EmployeeTaskController::class, 'edit'])->name('tasks.edit');
    Route::put('/tasks/{task}', [EmployeeTaskController::class, 'update'])->name('tasks.update');
    
    // Projects Routes
    Route::get('/projects', [EmployeeProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/{project}', [EmployeeProjectController::class, 'show'])->name('projects.show');
    
    // Expenses Routes
    Route::get('/expenses', [EmployeeExpenseController::class, 'index'])->name('expenses.index');
    Route::get('/expenses/{expense}', [EmployeeExpenseController::class, 'show'])->name('expenses.show');
    
    // Monthly Plans Routes (Only for Managers)
    Route::get('/monthly-plans', [EmployeeMonthlyPlanController::class, 'index'])->name('monthly-plans.index');
    Route::get('/monthly-plans/{monthlyPlan}', [EmployeeMonthlyPlanController::class, 'show'])->name('monthly-plans.show');
    
    // Tasks Routes - Update route to include destroy
    Route::delete('/tasks/{task}', [EmployeeTaskController::class, 'destroy'])->name('tasks.destroy');
    
    // Task Files Routes
    Route::get('/tasks/{task}/files/{file}/view', [EmployeeTaskController::class, 'viewFile'])->name('tasks.files.view');
    Route::get('/tasks/{task}/files/{file}/download', [EmployeeTaskController::class, 'downloadFile'])->name('tasks.files.download');
    Route::delete('/tasks/{task}/files/{file}', [EmployeeTaskController::class, 'deleteFile'])->name('tasks.files.delete');
});

// Webhook routes (no authentication required)
Route::post('/webhook/github', [WebhookController::class, 'github'])->name('webhook.github')->middleware('verify.github.webhook');
Route::get('/webhook/status', [WebhookController::class, 'status'])->name('webhook.status');

// API routes (no authentication required)
Route::get('/api/tasks', [TasksController::class, 'getTasks'])->name('api.tasks');
Route::put('/api/tasks/status', [TasksController::class, 'updateTaskStatus'])->name('api.tasks.update-status');
Route::get('/api/employees-with-tasks', [TasksController::class, 'getEmployeesWithTasks'])->name('api.employees-with-tasks');
Route::get('/api/account-managers-with-tasks', [TasksController::class, 'getAccountManagersWithTasks'])->name('api.account-managers-with-tasks');

require __DIR__.'/auth.php';
