<?php

namespace App\User\Service;

use App\User\Entity\UserIntegration;
use App\User\Provider\UserIntegrationProvider;
use App\User\Provider\UserProvider;
use App\User\Request\UserIntegrationRequest;
use Ramsey\Uuid\Uuid;

class UserIntegrationRequestManager
{
    public function __construct(
        private UserIntegrationManager $userIntegrationManager,
        private UserIntegrationProvider $userIntegrationProvider,
        private UserProvider $userProvider,
        private UserFacebookIntegrationManager $userFacebookIntegrationManager,
    ) {
    }

    public function createOrUpdateFromRequest(UserIntegrationRequest $userIntegrationRequest): UserIntegration
    {
        $userId = Uuid::fromString($userIntegrationRequest->userId);

        $userIntegration = $this->userIntegrationProvider->findOneByUserAndPlatform(
            $userId,
            $userIntegrationRequest->platform
        );

        if (null === $userIntegration) {
            $userIntegration = new UserIntegration();
        }

        $this->mapFromRequest($userIntegration, $userIntegrationRequest);

        $this->userIntegrationManager->save($userIntegration);

        $this->integratePlatform($userIntegration);

        return $userIntegration;
    }

    public function updateFromRequest(UserIntegration $userIntegration, UserIntegrationRequest $userIntegrationRequest): void
    {
        $this->mapFromRequest($userIntegration, $userIntegrationRequest);

        $this->userIntegrationManager->save($userIntegration);

        $this->integratePlatform($userIntegration);
    }

    public function mapFromRequest(UserIntegration $userIntegration, UserIntegrationRequest $userIntegrationRequest): void
    {
        if ($userIntegrationRequest->has('userId')) {
            $user = $this->userProvider->get(Uuid::fromString($userIntegrationRequest->userId));
            $userIntegration->setUserId($user->getId());
            $userIntegration->setUser($user);
        }

        if ($userIntegrationRequest->has('externalId')) {
            $userIntegration->setExternalId($userIntegrationRequest->externalId);
        }

        if ($userIntegrationRequest->has('platform')) {
            $userIntegration->setPlatform($userIntegrationRequest->platform);
        }

        if ($userIntegrationRequest->has('accessToken')) {
            $userIntegration->setAccessToken($userIntegrationRequest->accessToken);
        }
    }

    private function integratePlatform(UserIntegration $userIntegration)
    {
        switch ($userIntegration->getPlatform()) {
            case UserIntegration::PLATFORM_FACEBOOK:
                $this->userFacebookIntegrationManager->linkFacebookAccount(
                    $userIntegration->getUser(),
                    $userIntegration->getAccessToken(),
                );
                break;
        }
    }
}
