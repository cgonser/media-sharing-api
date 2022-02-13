<?php

namespace App\User\Service;

use App\User\Entity\UserSetting;
use App\User\Repository\UserSettingRepository;
use Ramsey\Uuid\UuidInterface;

class UserSettingManager
{
    private UserSettingRepository $userSettingRepository;

    public function __construct(
        UserSettingRepository $userSettingRepository
    ) {
        $this->userSettingRepository = $userSettingRepository;
    }

    public function create(UserSetting $userSetting): void
    {
        $this->userSettingRepository->save($userSetting);
    }

    public function update(UserSetting $userSetting): void
    {
        $this->userSettingRepository->save($userSetting);
    }

    public function delete(UserSetting $userSetting): void
    {
        $this->userSettingRepository->delete($userSetting);
    }

    public function set(UuidInterface $userId, string $name, ?string $value): UserSetting
    {
        $userSetting = $this->get($userId, $name);

        if (!$userSetting) {
            $userSetting = new UserSetting();
            $userSetting->setUserId($userId);
            $userSetting->setName($name);
        }

        $userSetting->setValue($value);

        $this->userSettingRepository->save($userSetting);

        return $userSetting;
    }

    public function get(UuidInterface $userId, string $name): ?UserSetting
    {
        return $this->userSettingRepository->findOneBy([
            'userId' => $userId,
            'name' => $name,
        ]);
    }

    public function getValue(UuidInterface $userId, string $name, ?string $defaultValue = null): ?string
    {
        $userSetting = $this->get($userId, $name);

        return $userSetting ? $userSetting->getValue() : $defaultValue;
    }
}
