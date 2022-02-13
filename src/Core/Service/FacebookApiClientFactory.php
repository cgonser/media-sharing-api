<?php

namespace App\Core\Service;

use App\User\Entity\UserIntegration;
use App\User\Exception\UserIntegrationNotFoundException;
use App\User\Provider\UserIntegrationProvider;
use FacebookAds\Api;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class FacebookApiClientFactory
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private UserIntegrationProvider $userIntegrationProvider,
        private LoggerInterface $logger,
    ) {
    }

    public function createInstance(
        string $facebookAppId,
        string $facebookAppSecret,
        ?string $accessToken = null,
    ): Api {
        if (null === $accessToken) {
            $accessToken = $this->getUserToken();
        }

        $this->logger->info('facebook_api.create_instance', [
            'accessToken' => $accessToken,
        ]);

        return Api::init($facebookAppId, $facebookAppSecret, $accessToken, false);
    }

    public function createInstanceForUser(UuidInterface $userId): Api
    {
        return $this->createInstance($this->getUserFacebookAccessToken($userId));
    }

    private function getUserToken(): ?string
    {
        $token = $this->tokenStorage->getToken();
        if (null === $token || $token instanceof AnonymousToken || !$token->isAuthenticated()) {
            $this->logger->warning('facebook_api.get_user_token', [
                'error' => 'missing user token',
            ]);

            return null;
        }

        $this->logger->info('facebook_api.get_user_token', [
            'user_id' => $token->getUser()->getId()->toString(),
        ]);

        try {
            return $this->getUserFacebookAccessToken(
                $token->getUser()->getId()
            );
        } catch (\Exception $e) {
            $this->logger->warning('facebook_api.get_user_token', [
                'user_id' => $token->getUser()->getId()->toString(),
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    private function getUserFacebookAccessToken(UuidInterface $userId): string
    {
        $userIntegration = $this->userIntegrationProvider->getByUserAndPlatform(
            $userId,
            UserIntegration::PLATFORM_FACEBOOK
        );

        return $userIntegration->getAccessToken();
    }
}
