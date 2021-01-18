<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class PrepGoogleCloudRunCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vote:prep-gcr';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prepares the environment for Google Cloud Run';

    /**
     * Indicates whether the command should be shown in the Artisan command list.
     *
     * @var bool
     */
    protected $hidden = true;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $userCount = User::query()->count();

        // Log count
        $this->line(sprintf(
            'There are <info>%d</> users.',
            $userCount
        ));

        // If there are no users yet
        if ($userCount > 0) {
            $this->line('Not running initial seeders');
            return 0;
        }

        $this->call('vote:create-users');
        $this->call('vote:assign-permissions');

        // All good
        return 0;
    }
}
