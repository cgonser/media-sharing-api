<?php

namespace App\Core\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class QueryStringConverter implements ParamConverterInterface
{
    public function apply(Request $request, ParamConverter $configuration): bool
    {
        $name = $configuration->getName();
        $class = $configuration->getClass();

        $reflectionClass = new \ReflectionClass($class);
        $object = $reflectionClass->newInstance();

        foreach ($reflectionClass->getProperties() as $property) {
            $propertyName = $property->getName();
            if (!$request->query->has($propertyName)) {
                continue;
            }

            $object->$propertyName = $request->query->get($propertyName);
        }

        $request->attributes->set($name, $object);

        return true;
    }

    public function supports(ParamConverter $configuration): bool
    {
        return 'querystring' === $configuration->getConverter();
    }
}
