<?php

namespace App\BaseDto;

readonly class BaseDto
{
  public function __construct(...$args) {}

  /**
   * Get the request array representation of the data
   * */
  public static function fromArray(array $data)
  {
    return new static(...$data);
  }

/**
 * Convert requests array
 */
  public function toArray(): array
  {
    return get_object_vars($this);
  }
}
