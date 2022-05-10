<?php

namespace App\Core\ParamConverter;

use App\Core\Exception\ApiJsonInputValidationException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class QueryStringConverter implements ParamConverterInterface
{
    public function __construct(
        private readonly ValidatorInterface $validator,
    ) {
    }

    public function apply(Request $request, ParamConverter $configuration): bool
    {
        $name = $configuration->getName();
        $class = $configuration->getClass();

        $reflectionClass = new \ReflectionClass($class);
        $object = $reflectionClass->newInstance();

        $queryParameters = $request->query->all();

        foreach ($reflectionClass->getProperties() as $property) {
            $propertyName = $property->getName();
            if (!$request->query->has($propertyName)) {
                continue;
            }

            $object->$propertyName = $queryParameters[$propertyName];
        }

        $errors = $this->validator->validate($object);

        if ($errors->count() > 0) {
            throw new ApiJsonInputValidationException($errors);
        }

        $request->attributes->set($name, $object);

        return true;
    }

    public function supports(ParamConverter $configuration): bool
    {
        return 'querystring' === $configuration->getConverter();
    }
}
