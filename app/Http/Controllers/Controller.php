<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

abstract class Controller
{
    protected function successResponse( string $message, mixed $data = null, int $code = 200 ): JsonResponse {
        return response()->json([
            'status'  => 'success',
            'message' => $message,
            'data'    => $data,
        ], $code);
    }
 
    protected function errorResponse( string $message, int $code = 400, mixed $data = null ): JsonResponse {
        return response()->json([
            'status'  => 'error',
            'message' => $message,
            'data'    => $data,
        ], $code);
    }
    
}
