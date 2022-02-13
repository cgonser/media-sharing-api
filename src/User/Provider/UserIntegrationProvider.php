<?php

namespace App\User\Provider;

use App\Core\Provider\AbstractProvider;
use App\User\Entity\UserIntegration;
use App\User\Exception\UserIntegrationNotFoundException;
use App\User\Repository\UserIntegrationRepository;
use Ramsey\Uuid\UuidInterface;

class UserIntegrationProvider extends AbstractProvider
{
    public function __construct(
        UserIntegrationRepository $repository,
    ) {
        $this->repository = $repository;
    }

    public function findByUser(UuidInterface $userId): array
    {
        return $this->repository->findBy([
            'userId' => $userId,
            'isActive' => true,
        ]);
    }

    public function findOneByUserAndPlatform(UuidInterface $userId, string $platform): ?UserIntegration
    {
        return $this->repository->findOneBy([
            'userId' => $userId,
            'platform' => $platform,
        ], [
            'createdAt' => 'DESC',
        ]);
    }

    public function findOneByExternalIdAndPlatform(string $externalId, string $platform): ?UserIntegration
    {
        return $this->repository->findOneBy([
            'externalId' => $externalId,
            'platform' => $platform,
        ]);
    }

    public function getByUserAndPlatform(UuidInterface $userId, string $platform): ?UserIntegration
    {
        $userIntegration = $this->findOneByUserAndPlatform($userId, $platform);

        if (null === $userIntegration
            || (null !== $userIntegration->getAccessTokenExpiresAt()
                && $userIntegration->getAccessTokenExpiresAt() < new \DateTime())) {
            $this->throwNotFoundException();
        }

        return $userIntegration;
    }

    protected function throwNotFoundException(): void
    {
        throw new UserIntegrationNotFoundException();
    }
}
