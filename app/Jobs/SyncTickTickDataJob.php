<?php

namespace App\Jobs;

use App\Services\TickTick\TickTickSyncService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\RateLimited;

class SyncTickTickDataJob implements ShouldQueue
{
    use Queueable;

    /**
     * Get the middleware the job should pass through.
     *
     * @return array<int, object>
     */
    public function middleware(): array
    {
        return [new RateLimited('ticktick-sync')];
    }

    public function handle(TickTickSyncService $syncService): void
    {
        $syncService->syncAll();
    }
}
