<?php

namespace App\Modules\Transaction\Resource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'amount' => $this->getFormattedAmountAttribute(),
            // 'wallet' => $this->whenLoaded('wallet', fn() => new WalletResource($this->wallet)),
            'status' => $this->status,
            'entry' => $this->entry,
            'reference_id' => $this->reference_id,
            'metadata' => $this->metadata
        ];
    }
}
