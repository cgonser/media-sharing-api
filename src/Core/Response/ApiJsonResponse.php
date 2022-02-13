<?php

namespace App\Core\Response;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiJsonResponse extends JsonResponse
{
    public function __construct(int $statusCode = Response::HTTP_OK, $data = null, $headers = [])
    {
        parent::__construct($this->format($data), $statusCode, $headers);
    }

    private function format($data = null)
    {
        if (null === $data) {
            return new \ArrayObject();
        }

        if (is_string($data)) {
            return [
                'message' => $data,
            ];
        }

        return $data;
    }
}
