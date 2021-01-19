<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\View;

class PrepareDockerPages extends Command
{
    private const ERROR_MAP = [
        [502, 'Bad Gateway'],
        [503, 'Service Unavailable'],
        [504, 'Gateway Timeout'],
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
        foreach (self::ERROR_MAP as [$htmlCode, $htmlText]) {
            $htmlFile = "error-{$htmlCode}.html";
            $this->line("Creating <info>{$htmlFile}</>...");

            $templateContents = View::make('docker.error', [
                'code' => $htmlCode,
                'title' => "{$htmlText} - HTTP {$htmlCode} - Gumbo e-voting",
                'message' => $htmlText,
            ])->render();

            if (file_put_contents(public_path($htmlFile), $templateContents)) {
                $this->info('OK');
                continue;
            }

            $this->error('Failed to write file 😢');
        }

        return 0;
    }
}
