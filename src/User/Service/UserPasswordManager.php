<?php

namespace App\User\Service;

use App\User\Entity\User;
use App\User\Entity\UserPasswordResetToken;
use App\User\Exception\UserInvalidPasswordException;
use App\User\Exception\UserPasswordResetTokenExpiredException;
use App\User\Exception\UserPasswordResetTokenNotFoundException;
use App\User\Repository\UserPasswordResetTokenRepository;
use App\User\Repository\UserRepository;
use Ramsey\Uuid\Uuid;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserPasswordManager
{
    /**
     * @var string
     */
    private const TOKEN_VALIDITY = '24 hours';

    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher,
        private UserRepository $userRepository,
        private UserPasswordResetTokenRepository $userPasswordResetTokenRepository,
        private UserEmailManager $userEmailManager,
    ) {
    }

    public function changePassword(User $user, string $currentPassword, string $newPassword): void
    {
        $isPasswordValid = $this->userPasswordHasher->isPasswordValid($user, $currentPassword);

        if (!$isPasswordValid) {
            throw new UserInvalidPasswordException();
        }

        $this->doChangePassword($user, $newPassword);

        $user->setPassword(
            $this->userPasswordHasher->hashPassword($user, $newPassword)
        );

        $this->userRepository->save($user);
    }

    public function doChangePassword(User $user, string $newPassword): void
    {
        $user->setPassword(
            $this->userPasswordHasher->hashPassword($user, $newPassword)
        );

        $this->userRepository->save($user);
    }

    public function encodePassword(User $user, string $password): string
    {
        return $this->userPasswordHasher->hashPassword($user, $password);
    }

    public function startPasswordReset(User $user): void
    {
        $expiresAt = new \DateTime();
        $expiresAt->modify('+'.self::TOKEN_VALIDITY);

        $userPasswordResetToken = new UserPasswordResetToken();
        $userPasswordResetToken->setUser($user);
        $userPasswordResetToken->setExpiresAt($expiresAt);
        $userPasswordResetToken->setToken($this->generateResetToken($userPasswordResetToken));

        $this->userPasswordResetTokenRepository->save($userPasswordResetToken);

        $this->userEmailManager->sendResetEmail($userPasswordResetToken);
    }

    private function generateResetToken(UserPasswordResetToken $userPasswordResetToken): string
    {
        $plainToken = $userPasswordResetToken->getUser()->getId()->toString();
        $plainToken .= '|'.Uuid::uuid4();
        $plainToken .= '|'.$userPasswordResetToken->getExpiresAt()->getTimestamp();

        return $this->encodePassword($userPasswordResetToken->getUser(), $plainToken);
    }

    public function resetPassword(User $user, string $token, string $password): void
    {
        $userPasswordResetToken = $this->userPasswordResetTokenRepository->findOneBy([
            'user' => $user,
            'token' => $token,
        ]);

        if (!$userPasswordResetToken) {
            throw new UserPasswordResetTokenNotFoundException();
        }

        if ((new \DateTime()) > $userPasswordResetToken->getExpiresAt()) {
            throw new UserPasswordResetTokenExpiredException();
        }

        $user->setPassword(
            $this->userPasswordHasher->hashPassword($user, $password)
        );

        $this->userRepository->save($user);

        $this->userPasswordResetTokenRepository->delete($userPasswordResetToken);
    }

    public function validatePassword($user, $password): bool
    {
        return $this->userPasswordHasher->isPasswordValid($user, $password);
    }
}
