<?php

namespace App\User\ParamConverter;

use App\User\Provider\UserProvider;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class UserEntityConverter implements ParamConverterInterface
{
    public function __construct(
        private UserProvider $userProvider,
    ) {
    }

    public function apply(Request $request, ParamConverter $configuration): bool
    {
        $request->attributes->set(
            'user',
            $this->userProvider->get(Uuid::fromString($request->attributes->get('userId')))
        );

        return true;
    }

    public function supports(ParamConverter $configuration): bool
    {
        return 'user.user_entity' === $configuration->getConverter();
    }
}
