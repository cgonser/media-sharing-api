<?php

namespace App\User\Service;

use App\Core\Service\FacebookApiClientFactory;
use App\User\Entity\User;
use App\User\Entity\UserIntegration;
use App\User\Exception\UserFacebookLoginFailedException;
use App\User\Provider\UserIntegrationProvider;
use FacebookAds\Api as FacebookApi;
use FacebookAds\Http\Exception\AuthorizationException;
use Ramsey\Uuid\UuidInterface;

class UserFacebookIntegrationManager
{
    public function __construct(
        private FacebookApiClientFactory $facebookApiClientFactory,
        private UserIntegrationProvider $userIntegrationProvider,
        private UserIntegrationManager $userIntegrationManager,
        private string $facebookAppId,
        private string $facebookAppSecret,
    ) {
    }

    public function linkFacebookAccount(User $user, string $accessToken): UserIntegration
    {
        try {
            $facebookApi = $this->facebookApiClientFactory->createInstance($accessToken);

            $response = $facebookApi->call('/me?fields=id,name,email,picture');

            $userIntegration = $this->createOrUpdateUserIntegration($user, $response->getContent(), $accessToken);

            $this->exchangeToken($userIntegration, $facebookApi);

            return $userIntegration;
        } catch (\Exception $e) {
            throw new UserFacebookLoginFailedException();
        }
    }

    private function createOrUpdateUserIntegration(User $user, array $graphUser, string $accessToken): UserIntegration
    {
        $userIntegration = $this->userIntegrationProvider->findOneByUserAndPlatform(
            $user->getId(),
            UserIntegration::PLATFORM_FACEBOOK
        );

        if (null === $userIntegration) {
            $userIntegration = new UserIntegration();
            $userIntegration->setUser($user);
            $userIntegration->setPlatform(UserIntegration::PLATFORM_FACEBOOK);
        }

        $userIntegration->setAccessToken($accessToken);
        $userIntegration->setExternalId($graphUser['id']);
        $userIntegration->setDetails($graphUser);

        $this->userIntegrationManager->save($userIntegration);

        return $userIntegration;
    }

    public function exchangeToken(UserIntegration $userIntegration, FacebookApi $facebookApi): void
    {
        $response = $facebookApi->call(
            '/oauth/access_token'.
            '?client_id='.$this->facebookAppId.
            '&client_secret='.$this->facebookAppSecret.
            '&grant_type=fb_exchange_token'.
            '&fb_exchange_token='.$userIntegration->getAccessToken()
        );

        $token = $response->getContent();
        $expiresIn = $token['expires_in'] ?? 60 * 60 * 24 * 30; // 30 days
        $expiresAt = new \DateTime('+'.$expiresIn.' seconds');

        $userIntegration->setAccessToken($token['access_token']);
        $userIntegration->setAccessTokenExpiresAt($expiresAt);

        $this->userIntegrationManager->save($userIntegration);
    }

    public function invalidateUserCurrentToken(UuidInterface $userId): void
    {
        $userIntegration = $this->userIntegrationProvider->findOneByUserAndPlatform(
            $userId,
            UserIntegration::PLATFORM_FACEBOOK
        );

        if ($userIntegration) {
            $this->userIntegrationManager->delete($userIntegration);
        }
    }

    public function validateToken(UserIntegration $userIntegration, FacebookApi $facebookApi): void
    {
        $response = null;

        try {
            $response = $this->doValidateToken($userIntegration, $facebookApi);
        } catch (AuthorizationException $e) {
            $errorResponse = json_decode($e->getResponse()->getBody());

            if (190 === $errorResponse->error->code) {
                $this->exchangeToken($userIntegration, $facebookApi);

                $response = $this->doValidateToken($userIntegration, $facebookApi);
            }
        } finally {
            if (null === $response) {
                $this->userIntegrationManager->delete($userIntegration);
            }
        }
    }

    private function doValidateToken(UserIntegration $userIntegration, FacebookApi $facebookApi): ?array
    {
        return $facebookApi->call(
            '/debug_token?'.
            'input_token='.$userIntegration->getAccessToken().
            '&access_token='.$userIntegration->getAccessToken()
        )->getContent();
    }
}
