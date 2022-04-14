<?php

namespace App\Localization\Request;

use App\Core\Request\AbstractRequest;
use OpenApi\Attributes as OA;

#[OA\RequestBody]
class CountryRequest extends AbstractRequest
{
    #[OA\Property]
    public ?string $name;

    #[OA\Property]
    public ?string $code;

    #[OA\Property]
    public ?string $currencyId;

    #[OA\Property]
    public ?string $primaryTimezone;

    #[OA\Property]
    public ?string $primaryLocale;

    #[OA\Property]
    public ?bool $isActive;
}
