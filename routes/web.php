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
use App\Http\Controllers\ContentCreationController;
use App\Http\Controllers\WorkIdeaController;
use App\Http\Controllers\WhatsAppTestController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::guard('web')->check()) {
        return redirect()->route('dashboard');
    }

    if (Auth::guard('employee')->check()) {
        $employee = Auth::guard('employee')->user();
        if ($employee && method_exists($employee, 'isWorkHubAdmin') && $employee->isWorkHubAdmin()) {
            return redirect()->route('employee.hub.index');
        }

        return redirect()->route('employee.dashboard');
    }

    $plans = \App\Models\Plan::with('features')
        ->active()
        ->ordered()
        ->get();
    return view('welcome', compact('plans'));
})->name('welcome');

Route::get('/content-creation', [ContentCreationController::class, 'index'])->name('content-creation.index');
Route::post('/content-creation/generate', [ContentCreationController::class, 'generateContent'])->name('content-creation.generate');
Route::post('/content-creation/upload-reference-image', [ContentCreationController::class, 'uploadReferenceImage'])->name('content-creation.upload-image');

// رابط عام لمساحة العمل — بدون تسجيل دخول
Route::get('/share/w/{token}', [\App\Http\Controllers\PublicWorkShareController::class, 'show'])->name('public.work.show');
Route::get('/share/w/{token}/gallery', [\App\Http\Controllers\PublicWorkShareController::class, 'showGallery'])->name('public.work.gallery');
Route::get('/share/w/{token}/gallery/pdf', [\App\Http\Controllers\PublicWorkShareController::class, 'downloadGalleryPdf'])->name('public.work.gallery.pdf');
Route::get('/share/w/{token}/ready-to-publish', [\App\Http\Controllers\PublicWorkShareController::class, 'showReadyToPublish'])->name('public.work.ready-to-publish');
Route::post('/share/w/{token}/t/{task}/publish-schedule', [\App\Http\Controllers\PublicWorkShareController::class, 'updatePublishSchedule'])->name('public.work.publish-schedule');
Route::get('/share/w/{token}/t/{task}', [\App\Http\Controllers\PublicWorkShareController::class, 'showTask'])->name('public.work.task');
Route::get('/share/w/{token}/t/{task}/files/{file}', [\App\Http\Controllers\PublicWorkShareController::class, 'showFile'])->name('public.work.file');
Route::get('/share/w/{token}/t/{task}/download-all', [\App\Http\Controllers\PublicWorkShareController::class, 'downloadAllFiles'])->name('public.work.files.download-all');

// الأفكار المقترحة (Inbox منفصل عن مساحة العمل)
Route::get('/ideas', [WorkIdeaController::class, 'index'])->name('ideas.index');
Route::post('/ideas', [WorkIdeaController::class, 'store'])->name('ideas.store');
Route::get('/ideas/{idea}/edit', [WorkIdeaController::class, 'edit'])->name('ideas.edit');
Route::put('/ideas/{idea}', [WorkIdeaController::class, 'update'])->name('ideas.update');
Route::delete('/ideas/{idea}', [WorkIdeaController::class, 'destroy'])->name('ideas.destroy');
Route::post('/ideas/{idea}/archive', [WorkIdeaController::class, 'archive'])->name('ideas.archive');
Route::post('/ideas/{idea}/convert', [WorkIdeaController::class, 'convertToWork'])->name('ideas.convert');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', \App\Http\Middleware\CheckTrialStatus::class])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Evolution API (WhatsApp) - test send message to group (admin only)
    Route::post('/profile/whatsapp/test', [WhatsAppTestController::class, 'sendTestMessage'])->name('profile.whatsapp.test');

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
           Route::post('employees/{employee}/login-as', [EmployeeController::class, 'loginAs'])->name('employees.login-as');
           
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
           
           // متابعة مهام الفريق (أدمن فقط)
           Route::get('team-tasks', [\App\Http\Controllers\TeamTasksMonitorController::class, 'index'])->name('team-tasks.index');

           // Academy Work Hub (مساحة العمل)
           Route::get('work', [\App\Http\Controllers\WorkActivityController::class, 'index'])->name('work.index');
           Route::get('work/archive', [\App\Http\Controllers\WorkActivityController::class, 'archive'])->name('work.archive');
           Route::post('work', [\App\Http\Controllers\WorkActivityController::class, 'store'])->name('work.store');
           Route::post('work/folders', [\App\Http\Controllers\WorkFolderController::class, 'store'])->name('work.folders.store');
           Route::put('work/folders/{folder}', [\App\Http\Controllers\WorkFolderController::class, 'update'])->name('work.folders.update');
           Route::delete('work/folders/{folder}', [\App\Http\Controllers\WorkFolderController::class, 'destroy'])->name('work.folders.destroy');
           Route::post('work/{work}/move-folder', [\App\Http\Controllers\WorkFolderController::class, 'moveActivity'])->name('work.move-folder');
           Route::post('work/{work}/archive', [\App\Http\Controllers\WorkActivityController::class, 'moveToArchive'])->name('work.archive-activity');
           Route::post('work/{work}/unarchive', [\App\Http\Controllers\WorkActivityController::class, 'restoreFromArchive'])->name('work.unarchive-activity');
           Route::get('work/{work}', [\App\Http\Controllers\WorkActivityController::class, 'show'])->name('work.show');
           Route::get('work/{work}/ready-to-publish', [\App\Http\Controllers\WorkActivityController::class, 'readyToPublish'])->name('work.ready-to-publish');
           Route::put('work/{work}', [\App\Http\Controllers\WorkActivityController::class, 'update'])->name('work.update');
           Route::delete('work/{work}', [\App\Http\Controllers\WorkActivityController::class, 'destroy'])->name('work.destroy');
           Route::post('work/{work}/share/enable', [\App\Http\Controllers\WorkActivityController::class, 'enableShare'])->name('work.share.enable');
           Route::post('work/{work}/share/regenerate', [\App\Http\Controllers\WorkActivityController::class, 'regenerateShare'])->name('work.share.regenerate');
           Route::post('work/{work}/share/disable', [\App\Http\Controllers\WorkActivityController::class, 'disableShare'])->name('work.share.disable');
           Route::post('work/{work}/tasks', [\App\Http\Controllers\WorkTaskController::class, 'store'])->name('work.tasks.store');
           Route::post('work/{work}/tasks/parse-bulk', [\App\Http\Controllers\WorkTaskController::class, 'parseBulk'])->name('work.tasks.parse-bulk');
           Route::post('work/{work}/tasks/reorder', [\App\Http\Controllers\WorkTaskController::class, 'reorder'])->name('work.tasks.reorder');
           Route::get('work/{work}/tasks/{task}', [\App\Http\Controllers\WorkTaskController::class, 'show'])->name('work.tasks.show');
           Route::get('work/{work}/tasks/{task}/edit', [\App\Http\Controllers\WorkTaskController::class, 'edit'])->name('work.tasks.edit');
           Route::put('work/{work}/tasks/{task}', [\App\Http\Controllers\WorkTaskController::class, 'update'])->name('work.tasks.update');
           Route::post('work/{work}/tasks/{task}/move-activity', [\App\Http\Controllers\WorkTaskController::class, 'moveToActivity'])->name('work.tasks.move-activity');
           Route::post('work/{work}/tasks/{task}/assign', [\App\Http\Controllers\WorkTaskController::class, 'assign'])->name('work.tasks.assign');
           Route::post('work/{work}/tasks/{task}/status', [\App\Http\Controllers\WorkTaskController::class, 'updateStatus'])->name('work.tasks.status');
           Route::post('work/{work}/tasks/{task}/duplicate', [\App\Http\Controllers\WorkTaskController::class, 'duplicate'])->name('work.tasks.duplicate');
           Route::post('work/{work}/tasks/{task}/publish-links', [\App\Http\Controllers\WorkTaskController::class, 'updatePublishLinks'])->name('work.tasks.publish-links');
           Route::post('work/{work}/tasks/{task}/publish-schedule', [\App\Http\Controllers\WorkTaskController::class, 'updatePublishSchedule'])->name('work.tasks.publish-schedule');
           Route::post('work/{work}/tasks/{task}/move-stage', [\App\Http\Controllers\WorkTaskController::class, 'moveStage'])->name('work.tasks.move-stage');
           Route::post('work/{work}/tasks/{task}/files', [\App\Http\Controllers\WorkTaskController::class, 'uploadFile'])->name('work.tasks.files.upload');
           Route::get('work/{work}/tasks/{task}/files/{file}/download', [\App\Http\Controllers\WorkTaskController::class, 'downloadFile'])->name('work.tasks.files.download');
           Route::delete('work/{work}/tasks/{task}/files/{file}', [\App\Http\Controllers\WorkTaskController::class, 'deleteFile'])->name('work.tasks.files.destroy');
           Route::post('work/{work}/tasks/{task}/summarize-designer', [\App\Http\Controllers\WorkTaskController::class, 'summarizeDesignerBrief'])->name('work.tasks.summarize-designer');
           Route::delete('work/{work}/tasks/{task}', [\App\Http\Controllers\WorkTaskController::class, 'destroy'])->name('work.tasks.destroy');

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
    Route::post('/stop-login-as', [EmployeeController::class, 'stopLoginAs'])->name('stop-login-as');
    
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

    // Academy Work Hub Tasks (مساحة العمل - مهامي)
    Route::get('/mine', [\App\Http\Controllers\Employee\EmployeeWorkTaskController::class, 'mine'])->name('mine');
    Route::get('/archive', [\App\Http\Controllers\Employee\EmployeeWorkTaskController::class, 'archive'])->name('archive');
    Route::get('/my-tasks', [\App\Http\Controllers\Employee\EmployeeWorkTaskController::class, 'index'])->name('tasks.index');
    Route::get('/activities/{work}', [\App\Http\Controllers\Employee\EmployeeWorkTaskController::class, 'showActivity'])->name('work.activity');
    Route::post('/activities/{work}/tasks/reorder', [\App\Http\Controllers\Employee\EmployeeWorkTaskController::class, 'reorder'])->name('work.reorder');
    Route::post('/activities/{work}/tasks/{task}/move-stage', [\App\Http\Controllers\Employee\EmployeeWorkTaskController::class, 'moveStage'])->name('work.move-stage');
    Route::get('/work-tasks/{task}', [\App\Http\Controllers\Employee\EmployeeWorkTaskController::class, 'show'])->name('work.show');
    Route::patch('/work-tasks/{task}/status', [\App\Http\Controllers\Employee\EmployeeWorkTaskController::class, 'updateStatus'])->name('work.status');
    Route::post('/work-tasks/{task}/files', [\App\Http\Controllers\Employee\EmployeeWorkTaskController::class, 'uploadFile'])->name('work.files.upload');
    Route::get('/work-tasks/{task}/files/{file}/download', [\App\Http\Controllers\Employee\EmployeeWorkTaskController::class, 'downloadFile'])->name('work.files.download');
    Route::delete('/work-tasks/{task}/files/{file}', [\App\Http\Controllers\Employee\EmployeeWorkTaskController::class, 'deleteFile'])->name('work.files.destroy');

    // مساحة العمل الكاملة للأكونت منجر (أدمن الحتة دي)
    Route::middleware('employee.work_hub')->prefix('hub')->name('hub.')->group(function () {
        Route::get('/', [\App\Http\Controllers\WorkActivityController::class, 'index'])->name('index');
        Route::get('/archive', [\App\Http\Controllers\WorkActivityController::class, 'archive'])->name('archive');
        Route::post('/', [\App\Http\Controllers\WorkActivityController::class, 'store'])->name('store');
        Route::post('/folders', [\App\Http\Controllers\WorkFolderController::class, 'store'])->name('folders.store');
        Route::put('/folders/{folder}', [\App\Http\Controllers\WorkFolderController::class, 'update'])->name('folders.update');
        Route::delete('/folders/{folder}', [\App\Http\Controllers\WorkFolderController::class, 'destroy'])->name('folders.destroy');
        Route::post('/{work}/move-folder', [\App\Http\Controllers\WorkFolderController::class, 'moveActivity'])->name('move-folder');
        Route::post('/{work}/archive', [\App\Http\Controllers\WorkActivityController::class, 'moveToArchive'])->name('archive-activity');
        Route::post('/{work}/unarchive', [\App\Http\Controllers\WorkActivityController::class, 'restoreFromArchive'])->name('unarchive-activity');
        Route::get('/{work}', [\App\Http\Controllers\WorkActivityController::class, 'show'])->name('show');
        Route::get('/{work}/ready-to-publish', [\App\Http\Controllers\WorkActivityController::class, 'readyToPublish'])->name('ready-to-publish');
        Route::put('/{work}', [\App\Http\Controllers\WorkActivityController::class, 'update'])->name('update');
        Route::delete('/{work}', [\App\Http\Controllers\WorkActivityController::class, 'destroy'])->name('destroy');
        Route::post('/{work}/share/enable', [\App\Http\Controllers\WorkActivityController::class, 'enableShare'])->name('share.enable');
        Route::post('/{work}/share/regenerate', [\App\Http\Controllers\WorkActivityController::class, 'regenerateShare'])->name('share.regenerate');
        Route::post('/{work}/share/disable', [\App\Http\Controllers\WorkActivityController::class, 'disableShare'])->name('share.disable');
        Route::post('/{work}/tasks', [\App\Http\Controllers\WorkTaskController::class, 'store'])->name('tasks.store');
        Route::post('/{work}/tasks/parse-bulk', [\App\Http\Controllers\WorkTaskController::class, 'parseBulk'])->name('tasks.parse-bulk');
        Route::post('/{work}/tasks/reorder', [\App\Http\Controllers\WorkTaskController::class, 'reorder'])->name('tasks.reorder');
        Route::get('/{work}/tasks/{task}', [\App\Http\Controllers\WorkTaskController::class, 'show'])->name('tasks.show');
        Route::get('/{work}/tasks/{task}/edit', [\App\Http\Controllers\WorkTaskController::class, 'edit'])->name('tasks.edit');
        Route::put('/{work}/tasks/{task}', [\App\Http\Controllers\WorkTaskController::class, 'update'])->name('tasks.update');
        Route::post('/{work}/tasks/{task}/move-activity', [\App\Http\Controllers\WorkTaskController::class, 'moveToActivity'])->name('tasks.move-activity');
        Route::post('/{work}/tasks/{task}/assign', [\App\Http\Controllers\WorkTaskController::class, 'assign'])->name('tasks.assign');
        Route::post('/{work}/tasks/{task}/status', [\App\Http\Controllers\WorkTaskController::class, 'updateStatus'])->name('tasks.status');
        Route::post('/{work}/tasks/{task}/duplicate', [\App\Http\Controllers\WorkTaskController::class, 'duplicate'])->name('tasks.duplicate');
        Route::post('/{work}/tasks/{task}/publish-links', [\App\Http\Controllers\WorkTaskController::class, 'updatePublishLinks'])->name('tasks.publish-links');
        Route::post('/{work}/tasks/{task}/publish-schedule', [\App\Http\Controllers\WorkTaskController::class, 'updatePublishSchedule'])->name('tasks.publish-schedule');
        Route::post('/{work}/tasks/{task}/move-stage', [\App\Http\Controllers\WorkTaskController::class, 'moveStage'])->name('tasks.move-stage');
        Route::post('/{work}/tasks/{task}/files', [\App\Http\Controllers\WorkTaskController::class, 'uploadFile'])->name('tasks.files.upload');
        Route::get('/{work}/tasks/{task}/files/{file}/download', [\App\Http\Controllers\WorkTaskController::class, 'downloadFile'])->name('tasks.files.download');
        Route::delete('/{work}/tasks/{task}/files/{file}', [\App\Http\Controllers\WorkTaskController::class, 'deleteFile'])->name('tasks.files.destroy');
        Route::post('/{work}/tasks/{task}/summarize-designer', [\App\Http\Controllers\WorkTaskController::class, 'summarizeDesignerBrief'])->name('tasks.summarize-designer');
        Route::delete('/{work}/tasks/{task}', [\App\Http\Controllers\WorkTaskController::class, 'destroy'])->name('tasks.destroy');
    });
    
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
