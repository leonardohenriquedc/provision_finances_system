<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProvisionController;
use App\Http\Controllers\ProvisionInstallmentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [ProvisionController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function (){
    Route::post('/provision', [ProvisionController::class, 'create']);
    
    Route::get('/provision', [ProvisionController::class, 'viewCreate'])->name('provision');
    Route::get('/provision/{id}', [ProvisionController::class, 'edit']);
    Route::get('/provisions', [ProvisionController::class, 'index'])->name('provisions');
    
    Route::put('/provision/{id}', [ProvisionController::class, 'update']);
    
    Route::delete('/provision/{id}', [ProvisionController::class, 'delete']);

    Route::get('installments/{id}', [ProvisionInstallmentController::class, 'index'])->name('installments');
    Route::get('/installment/{id}', [ProvisionInstallmentController::class, 'view'])->name('installment');
    Route::put('/installment/{id}', [ProvisionInstallmentController::class, 'updateInstallmentStatus']);
    Route::get('/periodinstallments', [ProvisionInstallmentController::class, 'viewCurrentInstallments'])->name('periodinstallments');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
