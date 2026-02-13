<?php

namespace App;

use Illuminate\Http\JsonResponse;

class ResponseData
{
    /**
     * Create a new class instance.
     */
    // public function __construct(array$data){}

    public function create(
        string $message,
        mixed $data = null,
        string $error = null,
        string $status = 'success',
        int $status_code = 200,
        bool $isJson = true,
    ): array| JsonResponse {
        $response = [
            'message' => $message,
            'status' => $status,
        ];
        if ($error) {
            $response['error'] = $error;
        }
        if ($data) {
            $response['data'] = $data;
        }
        if ($isJson) {
            return response()->json(
                $response,
                $status_code
            );
        } else {
            return $response;
        }
    }

}
