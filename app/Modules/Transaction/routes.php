<?php

use App\Modules\Transaction\Controllers\CreateTransactionController;
use App\Modules\Transaction\Controllers\QueryTransactionController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
  Route::post('transactions', [CreateTransactionController::class, 'create'])->name('transaction.create');
  Route::get('wallets', [QueryTransactionController::class, 'showWallet'])->name('wallet.show');
  Route::get('transactions', [QueryTransactionController::class, 'listTransactions'])->name('transaction.list');
  Route::get('transactions/export',[QueryTransactionController::class, 'exportTransactions'])->name('transactions.export');
});

