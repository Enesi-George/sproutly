<?php

namespace App\Modules\Transaction\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Transaction\Actions\CreateTransactionAction;
use App\Modules\Transaction\Dtos\TransactionDto;
use App\Modules\Transaction\Requests\TransactionRequest;
use App\Modules\Transaction\Resource\TransactionResource;
use App\Traits\ApiResponsesTrait;
use Illuminate\Support\Facades\Auth;

class CreateTransactionController extends Controller
{
    use ApiResponsesTrait;

    public function __construct(private readonly CreateTransactionAction $createTransactionAction) {}

    /**
     * Create transaction (amount in kobo)
     */
    public function create(TransactionRequest $request){
        $validatedBody = $request->validated();
        $validatedBody['user_id'] = Auth::id();

        $result = $this->createTransactionAction->create(TransactionDto::fromArray($validatedBody));

        return $this->successApiResponse('Transaction was successfull', [new TransactionResource($result['transaction'])] , 201);  
    }

}
