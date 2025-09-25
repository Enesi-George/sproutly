<?php

namespace App\Modules\Transaction\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class LogCompletedExport implements ShouldQueue
{
    use Queueable;

    protected string $filePath;
    protected string $userId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $filePath, string $userId)
    {
        $this->filePath = $filePath;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $fullPath = Storage::disk('local')->path($this->filePath);

        logger("Transaction export completed for user {$this->userId}. File path: {$fullPath}");
    }
}
