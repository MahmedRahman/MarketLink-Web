<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Clients Routes
    Route::resource('clients', ClientController::class);
    
    // Projects Routes
    Route::resource('projects', ProjectController::class);
    
    // Employees Routes
    Route::resource('employees', EmployeeController::class);
});

// Webhook routes (no authentication required)
Route::post('/webhook/github', [WebhookController::class, 'github'])->name('webhook.github')->middleware('verify.github.webhook');
Route::get('/webhook/status', [WebhookController::class, 'status'])->name('webhook.status');

require __DIR__.'/auth.php';
