<?php

namespace App\User\ResponseMapper;

use App\User\Dto\UserSettingDto;
use App\User\Entity\UserSetting;

class UserSettingResponseMapper
{
    public function map(UserSetting $userSetting): UserSettingDto
    {
        $userSettingDto = new UserSettingDto();
        $userSettingDto->name = $userSetting->getName();
        $userSettingDto->value = $userSetting->getValue();

        return $userSettingDto;
    }

    public function mapMultiple(array $userSettings): array
    {
        $userSettingDtos = [];

        foreach ($userSettings as $userSetting) {
            $userSettingDtos[] = $this->map($userSetting);
        }

        return $userSettingDtos;
    }
}
