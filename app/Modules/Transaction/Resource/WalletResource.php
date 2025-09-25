<?php

namespace App\Modules\Transaction\Resource;

use App\Modules\Auth\Resource\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletResource extends JsonResource
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
            'balance' => $this->getFormattedAmountAttribute(),
            'user' => $this->whenLoaded('user', [
                'name' => $this->user?->name,
                'email' => $this->user?->email,
            ]),
            'status' => $this->status
        ];
    }
}
