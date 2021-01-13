<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Response;

class KubernetesController extends Controller
{
    public function health(): HttpResponse
    {
        return Response::make('', HttpResponse::HTTP_NO_CONTENT);
    }
}
