<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Url;

class DeleteExpiredUrls extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'urls:delete-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete expired URLs from the database';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $deletedCount = Url::where('expires_at', '<', now())->delete();

        $this->info("Deleted {$deletedCount} expired URLs.");
    }
}
