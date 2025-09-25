<?php

namespace Database\Factories\Modules\Transaction\Models;

use App\Modules\Transaction\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\Transaction\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'amount' => $this->faker->numberBetween(100, 1000),
            'wallet_id' => null,
            'user_id' => null,
            'status' => 'success',
            'entry' => $this->faker->randomElement(['debit', 'credit']),
            'reference_id' => $this->faker->uuid(),
            'metadata' => [],
        ];
    }
}
