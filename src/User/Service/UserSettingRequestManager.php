<?php

namespace App\User\Service;

use App\User\Entity\UserSetting;
use App\User\Request\UserSettingRequest;
use Ramsey\Uuid\Uuid;

class UserSettingRequestManager
{
    private UserSettingManager $userSettingManager;

    public function __construct(
        UserSettingManager $userSettingManager
    ) {
        $this->userSettingManager = $userSettingManager;
    }

    public function createFromRequest(UserSettingRequest $userSettingRequest): UserSetting
    {
        $userSetting = new UserSetting();

        $this->mapFromRequest($userSetting, $userSettingRequest);

        $this->userSettingManager->create($userSetting);

        return $userSetting;
    }

    public function updateFromRequest(UserSetting $userSetting, UserSettingRequest $userSettingRequest): void
    {
        $this->mapFromRequest($userSetting, $userSettingRequest);

        $this->userSettingManager->update($userSetting);
    }

    private function mapFromRequest(UserSetting $userSetting, UserSettingRequest $userSettingRequest): void
    {
        if ($userSettingRequest->has('userId')) {
            $userSetting->setUserId(Uuid::fromString($userSettingRequest->userId));
        }

        if ($userSettingRequest->has('name')) {
            $userSetting->setName($userSettingRequest->name);
        }

        if ($userSettingRequest->has('value')) {
            $userSetting->setValue($userSettingRequest->value);
        }
    }
}
