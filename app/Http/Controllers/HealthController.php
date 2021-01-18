<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

class HealthController extends Controller
{
    public function index(): JsonResponse
    {
        return Response::json([
            'ok' => true,
        ], JsonResponse::HTTP_OK);
    }
}
