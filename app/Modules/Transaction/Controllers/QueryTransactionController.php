<?php

namespace App\Modules\Transaction\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Transaction\Actions\QueryTransactionAction;
use App\Modules\Transaction\Resource\TransactionResource;
use App\Modules\Transaction\Resource\WalletResource;
use App\Traits\ApiResponsesTrait;
use Illuminate\Http\Request;

class QueryTransactionController extends Controller
{
    use ApiResponsesTrait;

    public function __construct(private readonly QueryTransactionAction $queryTransactionAction) {}

    /**
     * Show user wallet
     */
    public function showWallet(Request $request){
        $user = $request->user();
            
        $result = $this->queryTransactionAction->showWallet($user);

        return $this->successApiResponse('Wallet return successfully', new WalletResource($result), 200);

    }

    /**
     * List user transactions
     */
    public function listTransactions(Request $request)
    {
        $user = $request->user();
        $result = $this->queryTransactionAction->listTransactions($user);

        return $this->paginatedApiResponse(TransactionResource::collection($result));
    }

    /**
     * Export user transactions history
     */
    public function exportTransactions()
    {
        $result = $this->queryTransactionAction->exportTransactions();
        return $this->successNoDataApiResponse('Transactions history exported successfully', 200);
    }

}
