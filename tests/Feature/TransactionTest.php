<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Transaction\Exports\TransactionExport;
use App\Modules\Transaction\Models\Transaction;
use App\Modules\Transaction\Models\Wallet;
use App\Services\KafkaProducerService;
use Junges\Kafka\Facades\Kafka;
use Maatwebsite\Excel\Facades\Excel;

test('authenticated user can view their wallet', function () {
    $user = User::factory()->create();
    $wallet = Wallet::factory()->create(['user_id' => $user->id, 'balance' => 10000]);

    $response = $this->actingAs($user, 'sanctum')->getJson(route('wallet.show'));
    $userId = $response->json('data.user.id');
    $email = $response->json('data.user.email');

    $response->assertStatus(200)
        ->assertJson([
            'status' => true,
            'message' => 'Wallet return successfully',
            'data' => [
                'id' => $wallet->id,
                'balance' => number_format($wallet->balance / 100, 2),
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'status' => 'active'
            ],
        ]);
});

test('authenticated user can list their transactions', function () {
    $user = User::factory()->create();
    $wallet = Wallet::factory()->create(['user_id' => $user->id]);

    $transactions = Transaction::factory()->count(3)->create([
        'user_id' => $user->id,
        'wallet_id' => $wallet->id,
    ]);

    $response = $this->actingAs($user, 'sanctum')->getJson(route('transaction.list'));

    $response->assertStatus(200)
        ->assertJson([
            'status' => true,
            'message' => 'List of transactions',
        ])
        ->assertJsonCount(3, 'data');
});

test('authenticated user can create a debit transaction', function () {
    $user = User::factory()->create();
    $wallet = Wallet::factory()->create(['user_id' => $user->id, 'balance' => 5000]);

    // Mock the Kafka service
    $kafkaService = $this->mock(KafkaProducerService::class);
    $kafkaService->shouldReceive('produceTransaction')
        ->once()
        ->with(\Mockery::on(function ($data) use ($user) {
            return $data['user_id'] === $user->id
                && $data['entry'] === 'debit'
                && $data['amount'] === '10.00'
                && $data['balance'] === '40.00'
                && isset($data['timestamp']);
        }))
        ->andReturn(true);

    $transactionData = [
        'amount' => 1000,
        'entry' => 'debit',
    ];

    $response = $this->actingAs($user, 'sanctum')->postJson(route('transaction.create'), $transactionData);

    $response->assertStatus(201)
        ->assertJson([
            'status' => true,
            'message' => 'Transaction was successfull',
        ]);

    $this->assertDatabaseHas('transactions', [
        'user_id' => $user->id,
        'wallet_id' => $wallet->id,
        'amount' => 1000,
        'entry' => 'debit',
        'status' => 'success',
    ]);

    $this->assertDatabaseHas('wallets', [
        'id' => $wallet->id,
        'balance' => 4000,
    ]);
});

test('authenticated user can create a credit transaction', function () {
    $user = User::factory()->create();
    $wallet = Wallet::factory()->create(['user_id' => $user->id, 'balance' => 5000]);

    // Mock the Kafka service
    $kafkaService = $this->mock(KafkaProducerService::class);
    $kafkaService->shouldReceive('produceTransaction')
        ->once()
        ->with(\Mockery::on(function ($data) use ($user) {
            return $data['user_id'] === $user->id
                && $data['entry'] === 'credit'
                && $data['amount'] === '10.00'
                && $data['balance'] === '60.00'
                && isset($data['timestamp']);
        }))
        ->andReturnNull();

    $transactionData = [
        'amount' => 1000,
        'entry' => 'credit',
    ];

    $response = $this->actingAs($user, 'sanctum')->postJson(route('transaction.create'), $transactionData);

    $response->assertStatus(201)
        ->assertJson([
            'status' => true,
            'message' => 'Transaction was successfull',
        ]);

    $this->assertDatabaseHas('transactions', [
        'user_id' => $user->id,
        'wallet_id' => $wallet->id,
        'amount' => 1000,
        'entry' => 'credit',
        'status' => 'success',
    ]);

    $this->assertDatabaseHas('wallets', [
        'id' => $wallet->id,
        'balance' => 6000,
    ]);
});



test('authenticated user cannot have a negative balance', function () {
    $user = User::factory()->create();
    $wallet = Wallet::factory()->create(['user_id' => $user->id, 'balance' => 5000]);

    $transactionData = [
        'amount' => 6000,
        'entry' => 'debit',
    ];

    $response = $this->actingAs($user, 'sanctum')->postJson(route('transaction.create'), $transactionData);

    $response->assertStatus(422)
        ->assertJson([
            'status' => 'failed',
            'errors' => [
                'amount' => ['Insufficient balance']
            ]
        ]);
});

test('authenticated user can export transactions', function () {
    Excel::fake();

    $user = User::factory()->create();
    $wallet = Wallet::factory()->create(['user_id' => $user->id]);

    Transaction::factory()->count(2)->create([
        'user_id' => $user->id,
        'wallet_id' => $wallet->id,
    ]);

    $response = $this->actingAs($user, 'sanctum')->getJson(route('transactions.export'));

    $response->assertStatus(200)
        ->assertJson([
            'status' => true,
            'message' => 'Transactions history exported successfully',
        ]);

    Excel::assertQueued('sproutly_transactions.xlsx', function ($export) {
        return $export instanceof TransactionExport;
    });
});
