<?php

declare(strict_types=1);

namespace App\User\EventSubscriber;

use App\User\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;

class CurrentUserRequestSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Security $security
    ) {
    }

    public function onControllerRequest(ControllerEvent $controllerEvent): void
    {
        $user = $this->security->getUser();
        $request = $controllerEvent->getRequest();

        if ('current' === $request->attributes->get('userId')) {
            if (!$user instanceof User) {
                throw new AccessDeniedHttpException();
            }

            $request->attributes->set('userId', $user->getId()->toString());
            $request->attributes->set('user', $user);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onControllerRequest',
        ];
    }
}
