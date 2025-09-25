<?php

namespace App\Modules\Transaction\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $fillable = [
        'id',
        'amount',
        'wallet_id',
        'status',
        'entry',
        'reference_id',
        'metadata',
        'user_id',
    ];

    protected $casts = [
        'metadata' => 'array',
        'amount' => 'integer',
    ];

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    //convert kobo to naira
    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount / 100, 2);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
