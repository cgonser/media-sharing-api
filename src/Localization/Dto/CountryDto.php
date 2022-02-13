<?php

namespace App\Localization\Dto;

use OpenApi\Annotations as OA;

class CountryDto
{
    public ?string $code;

    public ?string $name;

    public ?CurrencyDto $currency;

    public ?string $primaryLocale;

    public ?string $primaryTimezone;

    /**
     * @OA\Property(type="array", @OA\Items(type="string"))
     */
    public ?array $timezones;

    public ?bool $isActive;
}
