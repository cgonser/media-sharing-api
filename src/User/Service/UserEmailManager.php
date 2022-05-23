<?php

namespace App\User\Service;

use App\Core\Service\EmailComposer;
use App\Core\Service\UrlGenerator;
use App\User\Entity\User;
use App\User\Entity\UserPasswordResetToken;
use App\User\Exception\UserInvalidVerificationCodeException;
use App\User\Exception\UserNotFoundException;
use App\User\Provider\UserProvider;
use App\User\Request\UserFeedbackRequest;
use DateTime;
use DateTimeInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Mailer\MailerInterface;

class UserEmailManager
{
    private const EMAIL_VERIFICATION_URL_IDENTIFIER = 'email_verification';
    private const PASSWORD_RESET_URL_IDENTIFIER = 'password_reset';

    public function __construct(
        private readonly EmailComposer $emailComposer,
        private readonly MailerInterface $mailer,
        private readonly UrlGenerator $urlGenerator,
        private readonly UserProvider $userProvider,
        private readonly UserManager $userManager,
        private readonly string $contactRecipient,
    ) {
    }

    public function generateEmailVerificationToken(User $user): string
    {
        return base64_encode($user->getId()->toString().'|'.$user->getEmail());
    }

    public function generateEmailVerificationCode(User $user): string
    {
        return substr(
            base_convert(str_replace('-', '', $user->getId()->toString()), 16, 10),
            0,
            5
        );
    }

    public function generateEmailVerificationUrl(string $verificationToken): string
    {
        return $this->urlGenerator->generate(
            self::EMAIL_VERIFICATION_URL_IDENTIFIER,
            [
                'token' => $verificationToken,
            ]
        );
    }

    public function verifyEmailWithToken(string $verificationToken): void
    {
        [$userId, $emailAddress] = explode('|', base64_decode($verificationToken));

        /** @var User $user */
        $user = $this->userProvider->getBy([
            'id' => Uuid::fromString($userId),
            'email' => $emailAddress,
        ]);

        $this->markUserEmailAsValidated($user);
    }

    public function verifyEmailWithCode(UuidInterface $userId, string $verificationCode): void
    {
        /** @var User $user */
        $user = $this->userProvider->get($userId);

        if ($this->generateEmailVerificationCode($user) !== $verificationCode) {
            throw new UserInvalidVerificationCodeException();
        }

        $this->markUserEmailAsValidated($user);
    }

    private function markUserEmailAsValidated(User $user): void
    {
        $user->setIsEmailValidated(true);
        $user->setEmailValidatedAt(new DateTime());

        $this->userManager->update($user);
    }

    public function sendCreatedEmail(User $user): void
    {
        $verificationToken = $this->generateEmailVerificationToken($user);
        $verificationCode = $this->generateEmailVerificationCode($user);
        $verificationUrl = $this->generateEmailVerificationUrl($verificationToken);

        $this->mailer->send(
            $this->emailComposer->compose(
                'user.created',
                [
                    $user->getName() => $user->getEmail(),
                ],
                [
                    'username' => $user->getUsername(),
                    'greeting_name' => $user->getName(),
                    'email_verification_url' => $verificationUrl,
                    'email_verification_code' => $verificationCode,
                ],
                $user->getLocale()
            )
        );
    }

    public function sendAccountValidationEmail(User $user): void
    {
        $verificationToken = $this->generateEmailVerificationToken($user);
        $verificationCode = $this->generateEmailVerificationCode($user);
        $verificationUrl = $this->generateEmailVerificationUrl($verificationToken);

        $this->mailer->send(
            $this->emailComposer->compose(
                'user.email_validation_with_code',
                [
                    $user->getName() => $user->getEmail(),
                ],
                [
                    'username' => $user->getUsername(),
                    'greeting_name' => $user->getName(),
                    'email_verification_url' => $verificationUrl,
                    'email_verification_code' => $verificationCode,
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

    public function sendFeedbackEmail(User $user, UserFeedbackRequest $userFeedbackRequest): void
    {
        $email = $this->emailComposer->compose(
            'user.feedback',
            [
                $this->contactRecipient => $this->contactRecipient,
            ],
            [
                'username' => $user->getUsername(),
                'sentAt' => (new DateTime())->format(DateTimeInterface::ATOM),
                'type' => $userFeedbackRequest->type,
                'screen' => $userFeedbackRequest->screen,
                'description' => $userFeedbackRequest->description,
            ],
            $user->getLocale()
        );

        if (null !== $userFeedbackRequest->attachmentContents) {
            $email->attach(
                base64_decode($userFeedbackRequest->attachmentContents),
                $userFeedbackRequest->attachmentFilename,
            );
        }

        $this->mailer->send($email);
    }
}