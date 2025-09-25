<?php

namespace App\Modules\Auth\Dtos;

use App\BaseDto\BaseDto;

readonly class UserRegistrationDto extends BaseDto
{
  public function __construct(
    public readonly string $name,
    public readonly string $email,
    public readonly string $password,
  ) {}
}
