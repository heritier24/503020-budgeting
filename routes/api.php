<?php

use App\Http\Controllers\Api\BudgetController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ExportController;
use App\Http\Controllers\Api\IncomeController;
use App\Http\Controllers\Api\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth')->get('/user', function (Request $request) {
    return $request->user();
});

// Dashboard routes
Route::middleware('auth')->prefix('dashboard')->group(function () {
    Route::get('/overview', [DashboardController::class, 'overview']);
    Route::get('/spending-trends', [DashboardController::class, 'spendingTrends']);
    Route::get('/category-breakdown', [DashboardController::class, 'categoryBreakdown']);
    Route::get('/budget-performance', [DashboardController::class, 'budgetPerformance']);
});

// Income routes
Route::middleware('auth')->prefix('income')->group(function () {
    // Income sources
    Route::get('/sources', [IncomeController::class, 'sources']);
    Route::post('/sources', [IncomeController::class, 'storeSource']);
    Route::put('/sources/{incomeSource}', [IncomeController::class, 'updateSource']);
    Route::delete('/sources/{incomeSource}', [IncomeController::class, 'destroySource']);
    
    // Income records
    Route::get('/records', [IncomeController::class, 'records']);
    Route::post('/records', [IncomeController::class, 'storeRecord']);
    Route::put('/records/{incomeRecord}', [IncomeController::class, 'updateRecord']);
    Route::delete('/records/{incomeRecord}', [IncomeController::class, 'destroyRecord']);
    
    // Analytics
    Route::get('/analytics', [IncomeController::class, 'analytics']);
});

// Budget routes
Route::middleware('auth')->prefix('budget')->group(function () {
    Route::get('/config', [BudgetController::class, 'config']);
    Route::post('/config', [BudgetController::class, 'storeConfig']);
    Route::put('/config/{budgetConfig}', [BudgetController::class, 'updateConfig']);
    Route::get('/summary', [BudgetController::class, 'summary']);
    Route::get('/remaining', [BudgetController::class, 'remaining']);
});

// Category routes
Route::middleware('auth')->prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::post('/', [CategoryController::class, 'store']);
    Route::get('/{category}', [CategoryController::class, 'show']);
    Route::put('/{category}', [CategoryController::class, 'update']);
    Route::delete('/{category}', [CategoryController::class, 'destroy']);
});

// Transaction routes
Route::middleware('auth')->prefix('transactions')->group(function () {
    Route::get('/', [TransactionController::class, 'index']);
    Route::post('/', [TransactionController::class, 'store']);
    Route::get('/{transaction}', [TransactionController::class, 'show']);
    Route::put('/{transaction}', [TransactionController::class, 'update']);
    Route::delete('/{transaction}', [TransactionController::class, 'destroy']);
    Route::post('/bulk', [TransactionController::class, 'bulkStore']);
    Route::get('/export/csv', [TransactionController::class, 'exportCsv']);
});

// Export routes
Route::middleware('auth')->prefix('exports')->group(function () {
    Route::post('/transactions', [ExportController::class, 'exportTransactions']);
    Route::post('/income', [ExportController::class, 'exportIncome']);
    Route::post('/budget-report', [ExportController::class, 'exportBudgetReport']);
    Route::get('/history', [ExportController::class, 'getExportHistory']);
    Route::delete('/cleanup', [ExportController::class, 'cleanupOldExports']);
    Route::get('/download/{filename}', [ExportController::class, 'download'])->name('exports.download');
});
