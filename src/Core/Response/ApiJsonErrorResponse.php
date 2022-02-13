<?php

namespace App\Core\Response;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiJsonErrorResponse extends JsonResponse
{
    public function __construct(?int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR, ?string $message = null, $errors = [], $headers = [])
    {
        parent::__construct($this->format($message, $errors), $statusCode, $headers);
    }

    private function format(string $message = null, array $errors = [])
    {
        $data = [];

        if (null !== $message && strlen(trim($message)) > 0) {
            $data['message'] = $message;
        }

        if (count($errors) > 0) {
            $data['errors'] = $errors;
        }

        return $data;
    }
}
