<?php

namespace App\Core\Validation;

use App\Core\Exception\InvalidEntityException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EntityValidator
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    public function validate(object $entityObject)
    {
        $errors = $this->validator->validate($entityObject);

        if ($errors->count() > 0) {
            throw new InvalidEntityException($errors);
        }
    }
}
