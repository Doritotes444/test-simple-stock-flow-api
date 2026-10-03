<?php

declare(strict_types=1);

namespace App\Presentation\Http\ProblemDetails;

use Illuminate\Http\JsonResponse;

class ProblemDetailsResponse
{
    public static function create(
        string $title,
        string $detail,
        int $status = 400,
        array $invalidParams = []
    ): JsonResponse {
        $payload = [
            'type' => "https://tools.ietf.org/html/rfc7807",
            'title' => $title,
            'status' => $status,
            'detail' => $detail,
            'timestamp' => date('c'),
        ];

        if (!empty($invalidParams)) {
            $payload['invalid_params'] = $invalidParams;
        }

        return response()->json($payload, $status, [
            'Content-Type' => 'application/problem+json',
        ]);
    }
}
