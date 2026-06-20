<?php

namespace App\Traits;

trait ApiResponse
{
	public function successResponse($paginator = null, mixed $data = null, string $message = 'Success', int $status = 200)
	{
		return response()->json([
			'success' => true,
			'message' => $message,
			'data' => $data,
			'pagination' => [
				'current_page' => $paginator->currentPage(),
            	'last_page'    => $paginator->lastPage(),
	            'per_page'     => $paginator->perPage(),
	            'total'        => $paginator->total(),
			]
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