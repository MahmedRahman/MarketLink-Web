<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectRevenueController;
use App\Http\Controllers\ProjectExpenseController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\SubscriptionRequestController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $plans = \App\Models\Plan::with('features')
        ->active()
        ->ordered()
        ->get();
    return view('welcome', compact('plans'));
})->name('welcome');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', \App\Http\Middleware\CheckTrialStatus::class])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Clients Routes
    Route::resource('clients', ClientController::class);
    
           // Projects Routes
           Route::resource('projects', ProjectController::class);
           Route::post('projects/{project}/files', [ProjectController::class, 'uploadFile'])->name('projects.files.upload');
           Route::get('projects/{project}/files/{file}/download', [ProjectController::class, 'downloadFile'])->name('projects.files.download');
           Route::delete('projects/{project}/files/{file}', [ProjectController::class, 'deleteFile'])->name('projects.files.delete');
           
           // Project Revenues Routes
           Route::resource('projects.revenues', ProjectRevenueController::class);
           
           // Project Expenses Routes
           Route::resource('projects.expenses', ProjectExpenseController::class);
           
           // Employees Routes
           Route::resource('employees', EmployeeController::class);
           
           // Reports Routes
           Route::get('reports', [ReportsController::class, 'index'])->name('reports.index');
           Route::get('reports/export', [ReportsController::class, 'export'])->name('reports.export');
       });

// Subscription Routes
Route::middleware('auth')->group(function () {
    Route::get('/subscription', [SubscriptionController::class, 'index'])->name('subscription.index');
    Route::get('/subscription/expired', [SubscriptionController::class, 'expired'])->name('subscription.expired');
    Route::get('/subscription/plans', [SubscriptionController::class, 'plans'])->name('subscription.plans');
    Route::post('/subscription/subscribe', [SubscriptionController::class, 'subscribe'])->name('subscription.subscribe');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Users Routes
    Route::get('/users', [AdminController::class, 'users'])->name('users.index');
    Route::get('/users/{user}', [AdminController::class, 'showUser'])->name('users.show');
    Route::patch('/users/{user}/status', [AdminController::class, 'updateUserStatus'])->name('users.updateStatus');
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

// Webhook routes (no authentication required)
Route::post('/webhook/github', [WebhookController::class, 'github'])->name('webhook.github')->middleware('verify.github.webhook');
Route::get('/webhook/status', [WebhookController::class, 'status'])->name('webhook.status');

require __DIR__.'/auth.php';
