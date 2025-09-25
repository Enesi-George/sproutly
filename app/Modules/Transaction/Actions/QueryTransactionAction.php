<?php

namespace App\Modules\Transaction\Actions;

use App\Modules\Transaction\Exports\TransactionExport;
use App\Modules\Transaction\Jobs\LogCompletedExport;
use App\Modules\Transaction\Models\Transaction;
use App\Modules\Transaction\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class QueryTransactionAction
{

  public function showWallet($user)
  {
    $wallet = Wallet::where('user_id', $user->id)->firstOrFail();

    return $wallet;
  }

  public function listTransactions($user)
  {
    $transactions = Transaction::where('user_id', $user->id)->get();

    return $transactions;
  }

  public function exportTransactions()
  {
    $filePath = TransactionExport::generateFilePath();
    (new TransactionExport)
      ->queue($filePath)
      ->chain([
        new LogCompletedExport($filePath, Auth::id())
      ]);
  }
}
