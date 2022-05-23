<?php

namespace App\User\Service;

use App\Core\Response\ApiJsonResponse;
use App\User\Entity\User;
use App\User\Exception\UserInvalidRoleException;
use App\User\Exception\UserInvalidVerificationCodeException;
use App\User\Exception\UserNotFoundException;
use App\User\Exception\UserRoleNotFoundException;
use App\User\Provider\UserProvider;
use App\User\Request\UserEmailVerificationRequest;
use App\User\Request\UserPasswordChangeRequest;
use App\User\Request\UserPasswordResetRequest;
use App\User\Request\UserPasswordResetTokenRequest;
use App\User\Request\UserRequest;
use DateTime;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\HttpFoundation\Response;

class UserRequestManager
{
    public function __construct(
        private readonly UserManager $userManager,
        private readonly UserProvider $userProvider,
        private readonly UserPasswordManager $userPasswordManager,
        private readonly UserEmailManager $userEmailManager,
    ) {
    }

    public function createFromRequest(UserRequest $userRequest, ?string $ipAddress = null): User
    {
        $user = new User();

        $this->mapFromRequest($user, $userRequest);

        if (null !== $ipAddress) {
            $this->userManager->localizeUser($user, $ipAddress);
        } else {
            $this->userManager->applyDefaultLocalization($user);
        }

        $this->userManager->create($user);

        return $user;
    }

    public function updateFromRequest(User $user, UserRequest $userRequest): void
    {
        $this->mapFromRequest($user, $userRequest);

        $this->userManager->update($user);
    }

    public function mapFromRequest(User $user, UserRequest $userRequest): void
    {
        if ($userRequest->has('name')) {
            $user->setName($userRequest->name);
        }

        if ($userRequest->has('displayName')) {
            $user->setDisplayName($userRequest->displayName);
        }

        if ($userRequest->has('email')) {
            $user->setEmail(strtolower($userRequest->email));
        }

        if ($userRequest->has('username')) {
            $user->setUsername(strtolower($userRequest->username));
        }

        if ($userRequest->has('password') && null === $user->getPassword()) {
            $user->setPassword($this->userPasswordManager->encodePassword($user, $userRequest->password));
        }

        if ($userRequest->has('phoneNumber')) {
            $user->setPhoneNumber($userRequest->phoneNumber);
        }

        if ($userRequest->has('bio')) {
            $user->setBio($userRequest->bio);
        }

        if ($userRequest->has('country')) {
            $user->setCountry($userRequest->country);
        }

        if ($userRequest->has('locale')) {
            $user->setLocale($userRequest->locale);
        }

        if ($userRequest->has('timezone')) {
            $user->setTimezone($userRequest->timezone);
        }

        if ($userRequest->has('allowEmailMarketing')) {
            $user->setAllowEmailMarketing($userRequest->allowEmailMarketing);
        }

        if ($userRequest->has('isProfilePrivate')) {
            $user->setIsProfilePrivate($userRequest->isProfilePrivate);
        }

        if ($userRequest->has('isActive')) {
            $user->setIsActive($userRequest->isActive);
        }
    }

    public function changePassword(User $user, UserPasswordChangeRequest $userPasswordChangeRequest): void
    {
        $this->userPasswordManager->changePassword(
            $user,
            $userPasswordChangeRequest->currentPassword,
            $userPasswordChangeRequest->newPassword
        );
    }

    public function startPasswordReset(UserPasswordResetRequest $userPasswordResetRequest): void
    {
        $user = $this->userProvider->findOneByEmail(strtolower($userPasswordResetRequest->emailAddress));

        if (null === $user) {
            return;
        }

        $this->userPasswordManager->startPasswordReset($user);
    }

    public function concludePasswordReset(UserPasswordResetTokenRequest $userPasswordResetTokenRequest): void
    {
        [$emailAddress, $token] = explode('|', base64_decode($userPasswordResetTokenRequest->token));

        $user = $this->userProvider->findOneByEmail($emailAddress);

        if (null === $user) {
            throw new UserNotFoundException();
        }

        $this->userPasswordManager->resetPassword($user, $token, $userPasswordResetTokenRequest->password);
    }

    public function verifyEmailFromRequest(UserEmailVerificationRequest $userEmailVerificationRequest): void
    {
        if (null !== $userEmailVerificationRequest->token) {
            $this->userEmailManager->verifyEmailWithToken($userEmailVerificationRequest->token);

            return;
        }

        if (null !== $userEmailVerificationRequest->code && null !== $userEmailVerificationRequest->userId) {
            $this->userEmailManager->verifyEmailWithCode(
                Uuid::fromString($userEmailVerificationRequest->userId),
                $userEmailVerificationRequest->code
            );

            return;
        }

        throw new UserInvalidVerificationCodeException();
    }

    public function addRole(User $user, string $roleName): void
    {
        if (!in_array($roleName, [User::ROLE_USER, User::ROLE_ADMIN], true)) {
            throw new UserInvalidRoleException();
        }

        $user->addRole($roleName);

        $this->userManager->save($user);
    }

    public function removeRole(User $user, string $roleName): void
    {
        if (!$user->hasRole($roleName)) {
            throw new UserRoleNotFoundException();
        }

        $user->removeRole($roleName);

        $this->userManager->save($user);
    }
}
