<?php

namespace App\Traits;

trait ApiResponse
{
	protected function successResponse(mixed $data, string $message = '', int $status = 200)
	{
	    return response()->json([
	        'success' => true,
	        'message' => $message,
	        'data' => $data,
	    ], $status);
	}

	public function errorResponse(string $message, int $status = 400, array $errors = [])
	{
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }
}