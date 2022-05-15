<?php

namespace App\User\Service;

use App\Core\Service\EmailComposer;
use App\User\Entity\User;
use App\User\Entity\UserPasswordResetToken;
use App\User\Provider\UserProvider;
use Symfony\Component\Mailer\MailerInterface;

class UserEmailManager
{
    public function __construct(
        private readonly EmailComposer $emailComposer,
        private readonly MailerInterface $mailer,
        private readonly string $userEmailVerificationUrl,
        private readonly string $userPasswordResetUrl,
    ) {
    }

    public function sendCreatedEmail(User $user): void
    {
        $emailVerificationUrl = strtr(
            $this->userEmailVerificationUrl,
            [
                '%token%' => base64_encode($user->getId()->toString().'|'.$user->getEmail()),
            ]
        );

        $this->mailer->send(
            $this->emailComposer->compose(
                'user.created',
                [
                    $user->getName() => $user->getEmail(),
                ],
                [
                    'username' => $user->getUsername(),
                    'greeting_name' => $user->getName(),
                    'email_verification_url' => $emailVerificationUrl,
                ],
                $user->getLocale()
            )
        );
    }

    public function sendAccountValidationEmail(User $user): void
    {
        $emailVerificationUrl = strtr(
            $this->userEmailVerificationUrl,
            [
                '%token%' => base64_encode($user->getId()->toString().'|'.$user->getEmail()),
            ]
        );

        $this->mailer->send(
            $this->emailComposer->compose(
                'user.email_validation',
                [
                    $user->getName() => $user->getEmail(),
                ],
                [
                    'greeting_name' => $user->getName(),
                    'email_verification_url' => $emailVerificationUrl,
                ],
                $user->getLocale()
            )
        );
    }

    public function sendResetEmail(UserPasswordResetToken $userPasswordResetToken): void
    {
        $user = $userPasswordResetToken->getUser();

        $resetUrl = strtr(
            $this->userPasswordResetUrl,
            [
                '%token%' => base64_encode($user->getUsername().'|'.$userPasswordResetToken->getToken()),
            ]
        );

        $this->mailer->send(
            $this->emailComposer->compose(
                'user.password_reset',
                [
                    $user->getName() => $user->getEmail(),
                ],
                [
                    'greeting_name' => $user->getName(),
                    'reset_url' => $resetUrl,
                ],
                $user->getLocale()
            )
        );
    }
}