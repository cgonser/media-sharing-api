<?php

namespace App\User\Provider;

use App\Core\Provider\AbstractProvider;
use App\User\Entity\UserSetting;
use App\User\Exception\UserSettingNotFoundException;
use App\User\Repository\UserSettingRepository;
use Ramsey\Uuid\UuidInterface;

class UserSettingProvider extends AbstractProvider
{
    public function __construct(UserSettingRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getByUserAndId(UuidInterface $userId, UuidInterface $userSettingId): UserSetting
    {
        /** @var UserSetting|null $userSetting */
        $userSetting = $this->repository->findOneBy([
            'id' => $userSettingId,
            'userId' => $userId,
        ]);

        if (!$userSetting) {
            $this->throwNotFoundException();
        }

        return $userSetting;
    }

    protected function throwNotFoundException()
    {
        throw new UserSettingNotFoundException();
    }

    protected function getFilterableFields(): array
    {
        return [
            'userId',
        ];
    }
}
