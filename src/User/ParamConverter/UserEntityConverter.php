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
        $userId = $request->attributes->get('userId');
        $username = $request->attributes->get('username');

        if ('current' === $userId || 'current' === $username) {
            return true;
        }

        if (null !== $userId && Uuid::isValid($userId)) {
            $user = $this->userProvider->get(Uuid::fromString($userId));
        }

        if (null !== $username) {
            $user = $this->userProvider->getByUsername($username);
        }

        $request->attributes->set('user', $user ?? null);

        return true;
    }

    public function supports(ParamConverter $configuration): bool
    {
        return 'user.user_entity' === $configuration->getConverter();
    }
}
