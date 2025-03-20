<?php

namespace App\Jobs;

use App\Models\Url;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DeleteExpiredUrlsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        try {
            // Explicitly set timezone to Singapore
            $now = Carbon::now('Asia/Singapore');
            
            Log::info("Running URL cleanup job at: " . $now->toDateTimeString() . " (Singapore time)");
            
            // Count expired URLs
            $expiredCount = Url::where('expires_at', '<', $now)->count();
            
            Log::info("Found {$expiredCount} expired URLs");
            
            if ($expiredCount > 0) {
                // Delete expired URLs
                Url::where('expires_at', '<', $now)->delete();
                Log::info("Successfully deleted {$expiredCount} expired URLs");
            }
        } catch (\Exception $e) {
            Log::error("Error deleting expired URLs: " . $e->getMessage());
            $this->fail($e);
        }
    }
}
