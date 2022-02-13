<?php

namespace App\Localization\ResponseMapper;

use App\Localization\Dto\TimezoneDto;

class TimezoneResponseMapper
{
    public function map(array $timezone): TimezoneDto
    {
        $timezoneDto = new TimezoneDto();
        $timezoneDto->name = $timezone['name'];
        $timezoneDto->offsetGmt = $timezone['offset_gmt'];
        $timezoneDto->offsetRaw = $timezone['offset_raw'];

        return $timezoneDto;
    }

    public function mapMultiple(array $timezones): array
    {
        $timezoneDtos = [];

        foreach ($timezones as $timezone) {
            $timezoneDtos[] = $this->map($timezone);
        }

        return $timezoneDtos;
    }
}
