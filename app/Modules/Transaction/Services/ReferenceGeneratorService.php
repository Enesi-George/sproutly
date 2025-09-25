<?php

namespace App\Modules\Transaction\Services;

use App\Modules\Transaction\Models\Transaction;
use Illuminate\Support\Str;

class ReferenceGeneratorService
{

    public static function generate(string $prefix = 'REF-', int $length=15): string
    {
        do {
            $uniqueId = strtoupper(Str::ulid());

            $reference = $prefix . substr($uniqueId, 0, $length);

        } while (Transaction::where('reference_id', $reference)->exists());

        return $reference;
    }
}
