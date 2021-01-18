<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    /**
     * Flashes a message, allows arguments just like sprintf
     *
     * @param string $notice
     * @param scalar|null $args
     * @return void
     */
    protected function sendNotice(string $notice, ...$args): void
    {
        // Flashes the given message in the right key
        \request()->session()->put('message', sprintf($notice, ...$args));
    }
}
