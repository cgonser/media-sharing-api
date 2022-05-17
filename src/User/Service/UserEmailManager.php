<?php

namespace App\User\Service;

use App\Core\Service\EmailComposer;
use App\Core\Service\UrlGenerator;
use App\User\Entity\User;
use App\User\Entity\UserPasswordResetToken;
use App\User\Provider\UserProvider;
use Symfony\Component\Mailer\MailerInterface;

class UserEmailManager
{
    private const EMAIL_VERIFICATION_URL_IDENTIFIER = 'email_verification';
    private const PASSWORD_RESET_URL_IDENTIFIER = 'password_reset';

    public function __construct(
        private readonly EmailComposer $emailComposer,
        private readonly MailerInterface $mailer,
        private readonly UrlGenerator $urlGenerator,
    ) {
    }

    public function sendCreatedEmail(User $user): void
    {
        $url = $this->urlGenerator->generate(
            self::EMAIL_VERIFICATION_URL_IDENTIFIER,
            [
                'token' => base64_encode($user->getId()->toString().'|'.$user->getEmail()),
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
                    'email_verification_url' => $url,
                ],
                $user->getLocale()
            )
        );
    }

    public function sendAccountValidationEmail(User $user): void
    {
        $url = $this->urlGenerator->generate(
            self::EMAIL_VERIFICATION_URL_IDENTIFIER,
            [
                'token' => base64_encode($user->getId()->toString().'|'.$user->getEmail()),
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
                    'email_verification_url' => $url,
                ],
                $user->getLocale()
            )
        );
    }

    public function sendResetEmail(UserPasswordResetToken $userPasswordResetToken): void
    {
        $user = $userPasswordResetToken->getUser();

        $url = $this->urlGenerator->generate(
            self::PASSWORD_RESET_URL_IDENTIFIER,
            [
                'token' => base64_encode($user->getUsername().'|'.$userPasswordResetToken->getToken()),
            ]
        );

        $this->mailer->send(
            $this->emailComposer->compose(
                'user.password_reset',
                [
                    $user->getName() => $user->getEmail(),
                ],
                [
                    'username' => $user->getUsername(),
                    'greeting_name' => $user->getName(),
                    'reset_url' => $url,
                ],
                $user->getLocale()
            )
        );
    }
}