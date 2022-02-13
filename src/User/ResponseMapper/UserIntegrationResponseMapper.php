<?php

namespace App\User\ResponseMapper;

use App\User\Dto\UserIntegrationDto;
use App\User\Dto\UserIntegrationStatusDto;
use App\User\Entity\User;
use App\User\Entity\UserIntegration;

class UserIntegrationResponseMapper
{
    public function map(UserIntegration $userIntegration): UserIntegrationDto
    {
        $userIntegrationDto = new UserIntegrationDto();
        $userIntegrationDto->id = $userIntegration->getId()->toString();
        $userIntegrationDto->userId = $userIntegration->getUserId()->toString();
        $userIntegrationDto->platform = $userIntegration->getPlatform();
        $userIntegrationDto->externalId = $userIntegration->getExternalId();
        $userIntegrationDto->details = $userIntegration->getDetails();
        $userIntegrationDto->isActive = $userIntegration->isActive();
        $userIntegrationDto->accessTokenExpiresAt = $userIntegration->getAccessTokenExpiresAt()?->format(\DateTimeInterface::ATOM);
        $userIntegrationDto->createdAt = $userIntegration->getCreatedAt()->format(\DateTimeInterface::ATOM);
        $userIntegrationDto->updatedAt = $userIntegration->getUpdatedAt()->format(\DateTimeInterface::ATOM);

        return $userIntegrationDto;
    }

    public function mapMultiple(array $userIntegrations): array
    {
        $userIntegrationDtos = [];

        foreach ($userIntegrations as $userIntegration) {
            $userIntegrationDtos[] = $this->map($userIntegration);
        }

        return $userIntegrationDtos;
    }

    public function mapStatus(User $user, array $userIntegrations): UserIntegrationStatusDto
    {
        $userIntegrationStatusDto = new UserIntegrationStatusDto();
        $userIntegrationStatusDto->userId = $user->getId()->toString();
        $userIntegrationStatusDto->isEmailValidated = $user->isEmailValidated();

        /** @var UserIntegration $userIntegration */
        foreach ($userIntegrations as $userIntegration) {
            if (null !== $userIntegration->getExternalId()) {
                $userIntegrationStatusDto->platforms[$userIntegration->getPlatform()] = $userIntegration->getExternalId();
            }
        }

        return $userIntegrationStatusDto;
    }
}
