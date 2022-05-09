<?php

namespace App\Builder;

use Symfony\Component\HttpFoundation\JsonResponse;

class JsonResponseBuilder
{
    public function build(?array $data = null, int $status = 200): JsonResponse
    {
        return new JsonResponse($data, $status);
    }

    public function buildError(string $message, int $status = 400): JsonResponse
    {
        return $this->build(['error' => $message], $status);
    }

    public function buildPermissionDenied(): JsonResponse
    {
        return $this->buildError('Permission denied', 403);
    }
}
