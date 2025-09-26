<?php

namespace App\Modules\Transaction\Actions;

use App\Modules\Transaction\Models\Transaction;
use App\Modules\Transaction\Models\Wallet;
use App\Modules\Transaction\Services\ReferenceGeneratorService;
use App\Services\KafkaProducerService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateTransactionAction
{

  public function __construct(private readonly KafkaProducerService $kafkaProducerService) {}

  public function create($dto)
  {
    return DB::transaction(function () use ($dto) {
      $transactionData = $dto->toArray();

      // fetch user wallet and lock row to prevent race conditions
      $wallet = Wallet::where('user_id', Auth::id())->lockForUpdate()->firstOrFail();

      $transactionData['reference_id'] = ReferenceGeneratorService::generate();
      $transactionData['wallet_id'] = $wallet->id;
      $transactionData['user_id'] = Auth::id();
      $transactionData['status'] = 'pending';

      // Create transaction in pending
      $transaction = Transaction::create($transactionData);

      try {
        if ($transactionData['entry'] === "debit") {
          $newBalance = $wallet->balance - $transactionData['amount'];

          if ($newBalance < 0) {
            throw ValidationException::withMessages([
              'amount' => ['Insufficient balance']
            ]);
          }

          $wallet->balance = $newBalance;
        } elseif ($transactionData['entry'] === 'credit') {
          $wallet->balance += $transactionData['amount'];
        } else {
          throw ValidationException::withMessages([
            'entry' => ['Invalid transaction entry type. Must be debit or credit.']
          ]);
        }

        $wallet->save();

        $transaction->update(['status' => 'success']);

        $this->kafkaProducerService->produceTransaction([
          'user_id'   => $transaction->user_id,
          'entry'     => $transaction->entry,
          'amount'    => number_format($transaction->amount / 100, 2),
          'balance'   => number_format($wallet->balance / 100, 2),
          'timestamp' => now()->toISOString(),
        ]);
      } catch (\Throwable $e) {
        $transaction->update(['status' => 'fail']);
        throw $e;
      }

      return [
        'wallet'      => $wallet,
        'transaction' => $transaction->fresh(),
      ];
    });
  }
}
