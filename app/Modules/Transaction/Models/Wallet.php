<?php

namespace App\Modules\Transaction\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wallet extends Model
{
    use HasFactory, HasUlids, SoftDeletes;


    protected $fillable = [
        'id',
        'balance',
        'user_id',
        'status'
    ];

    //store kobo in naira
    protected $casts = [
        'balance' => 'integer',
    ];

    //convert kobo to naira
    public function getFormattedAmountAttribute():string
    {
        return number_format($this->balance / 100, 2);
    }

    public function transaction(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
