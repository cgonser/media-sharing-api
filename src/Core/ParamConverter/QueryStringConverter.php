<?php

namespace App\Core\ParamConverter;

use App\Core\Exception\ApiJsonInputValidationException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class QueryStringConverter implements ParamConverterInterface
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly DenormalizerInterface $denormalizer,
    ) {
    }

    public function apply(Request $request, ParamConverter $configuration): bool
    {
        $object = $this->denormalizer->denormalize($request->query->all(), $configuration->getClass());
        $errors = $this->validator->validate($object);

        if ($errors->count() > 0) {
            throw new ApiJsonInputValidationException($errors);
        }

        $request->attributes->set($configuration->getName(), $object);

        return true;
    }

    public function supports(ParamConverter $configuration): bool
    {
        return 'querystring' === $configuration->getConverter();
    }
}
