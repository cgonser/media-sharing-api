<?php

namespace App\Localization\Dto;

use OpenApi\Attributes as OA;

class CountryDto
{
    public ?string $code;

    public ?string $name;

    public ?CurrencyDto $currency;

    public ?string $primaryLocale;

    public ?string $primaryTimezone;

    #[OA\Property(type: "array", items: new OA\Items(type: "string"))]
    public ?array $timezones;

    public ?bool $isActive;
}
