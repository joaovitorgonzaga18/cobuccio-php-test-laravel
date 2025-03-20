<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

abstract class Controller
{
    public function buildResponse(array $response, int $status_code): JsonResponse {
        return response()->json(
            $response,
            $status_code
        );
    }
}
