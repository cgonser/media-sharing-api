<?php

namespace App\Core\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ApiJsonInputValidationException extends ApiJsonException
{
    public function __construct(
        ConstraintViolationListInterface $validationErrors,
        string $message = null,
        int $statusCode = Response::HTTP_BAD_REQUEST,
        \Throwable $previous = null
    ) {
        parent::__construct($statusCode, $message, $this->extractValidationMessages($validationErrors), $previous);
    }

    private function extractValidationMessages(ConstraintViolationListInterface $validationErrors): array
    {
        $errors = [];

        /** @var ConstraintViolationInterface $constraintViolation */
        foreach ($validationErrors as $constraintViolation) {
            $errors[] = [
                'propertyPath' => $constraintViolation->getPropertyPath(),
                'message' => $constraintViolation->getMessage(),
            ];
        }

        return $errors;
    }
}
