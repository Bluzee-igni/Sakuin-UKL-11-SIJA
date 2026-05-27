<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponse
{
    /**
     * Send a success response.
     *
     * @param mixed $data
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    protected function successResponse($data = null, string $message = 'Sukses', int $code = 200): JsonResponse
    {
        $response = [
            'berhasil' => true,
            'pesan' => $message,
            'data' => $data,
        ];

        return response()->json($response, $code);
    }

    /**
     * Send an error response.
     *
     * @param string $message
     * @param array $errors
     * @param int $code
     * @return JsonResponse
     */
    protected function errorResponse(string $message = 'Error', array $errors = [], int $code = 400): JsonResponse
    {
        $response = [
            'berhasil' => false,
            'pesan' => $message,
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Send a paginated response.
     *
     * @param LengthAwarePaginator $paginator
     * @param string $message
     * @return JsonResponse
     */
    protected function paginatedResponse(LengthAwarePaginator $paginator, string $message = 'Sukses'): JsonResponse
    {
        $response = [
            'berhasil' => true,
            'pesan' => $message,
            'data' => $paginator->items(),
            'meta' => [
                'halaman_saat_ini' => $paginator->currentPage(),
                'total' => $paginator->total(),
                'per_halaman' => $paginator->perPage(),
                'halaman_terakhir' => $paginator->lastPage(),
            ],
        ];

        return response()->json($response, 200);
    }
}
