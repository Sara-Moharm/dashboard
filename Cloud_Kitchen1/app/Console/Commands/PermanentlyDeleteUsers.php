<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class PermanentlyDeleteUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:permanently-delete-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        User::onlyTrashed()
        ->where('deleted_at', '<=', now()->subDays(30))            
        ->each(function ($user) {
            if ($user->isCustomer()) {
                $user->forceDelete();
            }
            });
    }
}
