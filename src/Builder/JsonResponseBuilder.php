<?php

namespace App\Builder;

use Symfony\Component\HttpFoundation\JsonResponse;

class JsonResponseBuilder
{
    public function build(?array $data = null, int $status = 200): JsonResponse
    {
        return new JsonResponse($data, $status);
    }
}
