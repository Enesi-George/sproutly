<?php

namespace App\Services;

use Junges\Kafka\Facades\Kafka;
use Exception;
use Illuminate\Support\Facades\Log;

class KafkaProducerService
{
    public function produceTransaction(array $transactionData): void
    {
        try {
            Kafka::publish()
                ->onTopic('transactions')
                ->withConfigOptions([
                    'message.timeout.ms' => 10000,
                    'request.timeout.ms' => 5000, 
                    'delivery.timeout.ms' => 10000,
                    'socket.timeout.ms' => 5000,  
                ])
                ->withBodyKey('transaction', $transactionData)
                ->send();
                
            Log::info('Transaction published to Kafka successfully');
        } catch (Exception $e) {
            Log::error('Failed to send transaction to Kafka', [
                'error' => $e->getMessage(),
                'transaction_data' => $transactionData
            ]);
            
            // throw $e;
        }
    }
}