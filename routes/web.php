<?php

use App\Http\Controllers\BorrowerController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

// Root: send authenticated users to dashboard, everyone else to login
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
})->name('home');

// All authenticated routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Equipment - all auth users can view. Admin-only writes enforced inside controller.
    // Status quick-update is available to BOTH admin and staff so they can manually mark
    // a borrowed item as available (e.g., when physical return happens but transaction wasn't logged).
    Route::patch('/equipment/{equipment}/status', [EquipmentController::class, 'updateStatus'])
        ->name('equipment.status');
    Route::resource('equipment', EquipmentController::class)->parameters([
        'equipment' => 'equipment',
    ]);

    // Borrowers
    Route::resource('borrowers', BorrowerController::class);

    // Borrowing transactions
    Route::prefix('borrowings')->name('borrowings.')->group(function () {
        Route::get('/', [BorrowingController::class, 'index'])->name('index');
        Route::get('/overdue', [BorrowingController::class, 'overdue'])->name('overdue');
        Route::get('/damaged', [BorrowingController::class, 'damaged'])->name('damaged');
        Route::get('/create', [BorrowingController::class, 'create'])->name('create');
        Route::post('/', [BorrowingController::class, 'store'])->name('store');
        Route::get('/{borrowing}', [BorrowingController::class, 'show'])->name('show');
        Route::get('/{borrowing}/return', [BorrowingController::class, 'returnForm'])
            ->middleware('role:admin')->name('return.form');
        Route::post('/{borrowing}/return', [BorrowingController::class, 'processReturn'])
            ->middleware('role:admin')->name('return.process');
        
        // Borrower-initiated actions
        Route::get('/request/create', [BorrowingController::class, 'borrowerRequestCreate'])
            ->middleware('role:borrower')->name('request.create');
        Route::post('/request/store', [BorrowingController::class, 'borrowerRequestStore'])
            ->middleware('role:borrower')->name('request.store');
        Route::get('/{borrowing}/request-return', [BorrowingController::class, 'borrowerReturnForm'])
            ->middleware('role:borrower')->name('request.return');
        Route::post('/{borrowing}/request-return', [BorrowingController::class, 'borrowerProcessReturn'])
            ->middleware('role:borrower')->name('request.return.process');
    });

    // Reports - admin only
    Route::middleware('role:admin')->prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/export/transactions', [ReportController::class, 'exportTransactions'])->name('export.transactions');
    });
});
