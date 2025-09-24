<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\GoalsController;
use App\Http\Controllers\AdminController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Landing', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'onboarding'])
    ->name('dashboard');

Route::get('/analytics', [AnalyticsController::class, 'index'])
    ->middleware(['auth', 'verified', 'onboarding'])
    ->name('analytics');

Route::get('/onboarding', [OnboardingController::class, 'index'])
    ->middleware(['auth', 'verified', 'onboarding'])
    ->name('onboarding');


Route::post('/onboarding/complete', [OnboardingController::class, 'complete'])
    ->middleware(['auth', 'verified'])
    ->name('onboarding.complete');

// Transaction routes
Route::middleware(['auth', 'verified', 'onboarding'])->group(function () {
    Route::get('/transactions', [App\Http\Controllers\TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/create', [App\Http\Controllers\TransactionController::class, 'create'])->name('transactions.create');
    Route::post('/transactions', [App\Http\Controllers\TransactionController::class, 'store'])->name('transactions.store');
    Route::put('/transactions/{transaction}', [App\Http\Controllers\TransactionController::class, 'update'])->name('transactions.update');
    Route::delete('/transactions/{transaction}', [App\Http\Controllers\TransactionController::class, 'destroy'])->name('transactions.destroy');
    Route::get('/transactions/statistics', [App\Http\Controllers\TransactionController::class, 'statistics'])->name('transactions.statistics');
    
    // Category routes
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Loan routes
    Route::get('/loans', [LoanController::class, 'index'])->name('loans.index');
    Route::get('/loans/create', [LoanController::class, 'create'])->name('loans.create');
    Route::post('/loans', [LoanController::class, 'store'])->name('loans.store');
    Route::get('/loans/{loan}', [LoanController::class, 'show'])->name('loans.show');
    Route::get('/loans/{loan}/edit', [LoanController::class, 'edit'])->name('loans.edit');
    Route::get('/loans/{loan}/payment', [LoanController::class, 'payment'])->name('loans.payment');
    Route::put('/loans/{loan}', [LoanController::class, 'update'])->name('loans.update');
    Route::delete('/loans/{loan}', [LoanController::class, 'destroy'])->name('loans.destroy');
    Route::post('/loans/{loan}/payments', [LoanController::class, 'recordPayment'])->name('loans.payments');

    // Goals routes
    Route::get('/goals', [GoalsController::class, 'index'])->name('goals.index');
    Route::get('/goals/create', [GoalsController::class, 'create'])->name('goals.create');
    Route::post('/goals', [GoalsController::class, 'store'])->name('goals.store');
    Route::get('/goals/{goal}', [GoalsController::class, 'show'])->name('goals.show');
    Route::get('/goals/{goal}/edit', [GoalsController::class, 'edit'])->name('goals.edit');
    Route::put('/goals/{goal}', [GoalsController::class, 'update'])->name('goals.update');
    Route::delete('/goals/{goal}', [GoalsController::class, 'destroy'])->name('goals.destroy');
    Route::post('/goals/{goal}/progress', [GoalsController::class, 'updateProgress'])->name('goals.progress');
    Route::post('/goals/{goal}/contribute', [GoalsController::class, 'contribute'])->name('goals.contribute');
    Route::get('/goals/{goal}/contributions', [GoalsController::class, 'getContributions'])->name('goals.contributions');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/analytics', [AdminController::class, 'analytics'])->name('analytics');
    Route::get('/system-health', [AdminController::class, 'systemHealth'])->name('system-health');
});

require __DIR__.'/auth.php';
