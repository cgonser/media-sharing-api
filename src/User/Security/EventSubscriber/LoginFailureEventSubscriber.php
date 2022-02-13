<?php

namespace App\User\Security\EventSubscriber;

use App\User\Exception\UserMissingPasswordException;
use App\User\Service\UserPasswordManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;

class LoginFailureEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private UserPasswordManager $userPasswordManager
    ) {
    }

    public function onLoginFailure(LoginFailureEvent $event): void
    {
        $user = $event->getPassport()?->getUser();

        if (!$user || null !== $user->getPassword()) {
            return;
        }

        $this->userPasswordManager->startPasswordReset($user);

        throw new UserMissingPasswordException();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginFailureEvent::class => 'onLoginFailure',
        ];
    }
}
