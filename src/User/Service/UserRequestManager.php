<?php

namespace App\User\Service;

use App\Core\Service\ImageUploader;
use App\User\Entity\User;
use App\User\Exception\UserInvalidRoleException;
use App\User\Exception\UserNotFoundException;
use App\User\Exception\UserRoleNotFoundException;
use App\User\Provider\UserProvider;
use App\User\Request\UserEmailVerificationRequest;
use App\User\Request\UserPasswordChangeRequest;
use App\User\Request\UserPasswordResetRequest;
use App\User\Request\UserPasswordResetTokenRequest;
use App\User\Request\UserRequest;
use Ramsey\Uuid\Uuid;

class UserRequestManager
{
    public function __construct(
        private UserManager $userManager,
        private UserProvider $userProvider,
        private UserPasswordManager $userPasswordManager,
        private ImageUploader $userImageUploader,
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

        if ($userRequest->has('email')) {
            $user->setEmail(strtolower($userRequest->email));
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

        if ($userRequest->has('profilePicture')) {
            $user->setProfilePicture(
                $this->userImageUploader->uploadImage($userRequest->profilePicture)
            );
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

    public function verifyEmail(UserEmailVerificationRequest $userEmailVerificationRequest): void
    {
        [$userId, $emailAddress] = explode('|', base64_decode($userEmailVerificationRequest->token));

        /** @var User $user */
        $user = $this->userProvider->get(Uuid::fromString($userId));

        if ($user->getEmail() !== $emailAddress) {
            throw new UserNotFoundException();
        }

        $user->setIsEmailValidated(true);
        $user->setEmailValidatedAt(new \DateTime());

        $this->userManager->update($user);
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
