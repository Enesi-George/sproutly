<?php

namespace App\Modules\Transaction\Dtos;

use App\BaseDto\BaseDto;

readonly class TransactionDto extends BaseDto
{

  public function __construct(
    public readonly int $amount,
    public readonly string $entry,
    public readonly string $user_id,
    public readonly ?array $metadata = null,
  ) {}
}
