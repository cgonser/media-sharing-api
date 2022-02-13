<?php

namespace App\Localization\Provider;

use Symfony\Component\Intl\Timezones;

class TimezoneProvider
{
    public function findAll(): array
    {
        return $this->formatResultset(Timezones::getIds());
    }

    public function findByCountryCode(string $countryCode): array
    {
        return $this->formatResultset(Timezones::forCountryCode($countryCode));
    }

    private function formatResultset(array $timezones): array
    {
        $timezones = array_map(function ($timezone) {
            return $this->prepareTimezone($timezone);
        }, $timezones);

        usort($timezones, static function ($a, $b) {
            if ($a['offset_raw'] === $b['offset_raw']) {
                return ($a['name'] < $b['name']) ? -1 : 1;
            }

            return ($a['offset_raw'] < $b['offset_raw']) ? -1 : 1;
        });

        return $timezones;
    }

    private function prepareTimezone(string $timezone): array
    {
        return [
            'name' => $timezone,
            'offset_gmt' => Timezones::getGmtOffset($timezone),
            'offset_raw' => Timezones::getRawOffset($timezone),
        ];
    }
}
