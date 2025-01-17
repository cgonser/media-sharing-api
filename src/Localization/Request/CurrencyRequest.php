<?php

namespace App\Localization\Request;

use App\Core\Request\AbstractRequest;
use OpenApi\Attributes as OA;

#[OA\RequestBody]
class CurrencyRequest extends AbstractRequest
{
    #[OA\Property]
    public ?string $name;

    #[OA\Property]
    public ?string $code;
}
