<?php

namespace App\Localization\Request;

use App\Core\Request\SearchRequest;
use OpenApi\Attributes as OA;

#[OA\RequestBody()]
class TimezoneSearchRequest extends SearchRequest
{
    #[OA\Property()]
    public ?string $countryCode = null;
}
