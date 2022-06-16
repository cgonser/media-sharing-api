<?php

namespace App\User\MessageHandler;

use App\User\Entity\User;
use App\User\Service\UserEmailManager;
use DateTime;
use DateTimeInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserLoggedInHandler implements EventSubscriberInterface
{
    public function __construct(
        private readonly UserEmailManager $userEmailManager,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event)
    {
        /** @var User $user */
        $user = $event->getUser();

        if (!$user instanceof User) {
            return;
        }

        $this->logger->info(
            'user.logged_in',
            [
                'userId' => $user->getId()->toString(),
                'loggedInAt' => (new DateTime())->format(DateTimeInterface::ATOM),
            ]
        );

        if (!$user->isEmailValidated()) {
            $this->userEmailManager->sendAccountValidationEmail($user);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Events::AUTHENTICATION_SUCCESS => 'onAuthenticationSuccess',
        ];
    }
}
