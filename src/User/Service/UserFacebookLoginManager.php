<?php

namespace App\User\Service;

use App\Core\Service\FacebookApiClientFactory;
use App\User\Entity\User;
use App\User\Entity\UserIntegration;
use App\User\Exception\UserFacebookLoginFailedException;
use App\User\Provider\UserIntegrationProvider;
use App\User\Provider\UserProvider;

class UserFacebookLoginManager
{
    public function __construct(
        private FacebookApiClientFactory $facebookApiClientFactory,
        private UserProvider $userProvider,
        private UserManager $userManager,
        private UserIntegrationProvider $userIntegrationProvider,
        private UserFollowManager $userIntegrationManager,
    ) {
    }

    public function prepareUserFromFacebookToken(string $accessToken, ?User $user = null, ?string $ipAddress = null): User
    {
        try {
            $facebookApi = $this->facebookApiClientFactory->createInstance($accessToken);

            $response = $facebookApi->call('/me?fields=id,name,email,picture');

            $graphUser = $response->getContent();

            if (null === $user) {
                $user = $this->createOrUpdateUserFromGraphUser($graphUser, $ipAddress);
            }

            $this->createOrUpdateUserIntegration($user, $graphUser, $accessToken);

            return $user;
        } catch (\Exception) {
            throw new UserFacebookLoginFailedException();
        }
    }

    private function createOrUpdateUserIntegration(User $user, array $graphUser, string $accessToken): void
    {
        $userIntegration = $this->userIntegrationProvider->findOneByUserAndPlatform(
            $user->getId(),
            UserIntegration::PLATFORM_FACEBOOK
        );

        if (null === $userIntegration) {
            $userIntegration = new UserIntegration();
            $userIntegration->setUser($user);
            $userIntegration->setExternalId($graphUser['id']);
            $userIntegration->setPlatform(UserIntegration::PLATFORM_FACEBOOK);
        }

        $userIntegration->setAccessToken($accessToken);
        $userIntegration->setDetails($graphUser);

        $this->userIntegrationManager->save($userIntegration);
    }

    private function createOrUpdateUserFromGraphUser(array $graphUser, ?string $ipAddress = null): User
    {
        $userIntegration = $this->userIntegrationProvider->findOneByExternalIdAndPlatform(
            $graphUser['id'],
            UserIntegration::PLATFORM_FACEBOOK
        );

        if (null !== $userIntegration) {
            return $userIntegration->getUser();
        }

        $user = new User();
        $user->setName($graphUser['name']);
        $user->setEmail($graphUser['email']);

        if (null !== $ipAddress) {
            $this->userManager->localizeUser($user, $ipAddress);
        }

        $this->userManager->create($user);

        return $user;
    }
}
