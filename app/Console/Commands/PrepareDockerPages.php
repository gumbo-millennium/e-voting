<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\View;

class PrepareDockerPages extends Command
{
    private const VIEW_MAP = [
        'docker.503' => '503-nginx.html',
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'docker:prepare-pages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates pages used by Docker';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        foreach (self::VIEW_MAP as $template => $htmlFile) {
            $this->line("Creating <info>{$htmlFile}</>...");

            $templateContents = View::make($template)->render();

            file_put_contents(public_path($htmlFile), $templateContents);

            $this->info('OK');
        }

        return 0;
    }
}
