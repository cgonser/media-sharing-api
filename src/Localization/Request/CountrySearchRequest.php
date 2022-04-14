<?php

namespace App\Localization\Request;

use App\Core\Request\SearchRequest;
use OpenApi\Attributes as OA;

#[OA\RequestBody]
class CountrySearchRequest extends SearchRequest
{
    #[OA\Property]
    public ?string $orderProperty = 'code';

    #[OA\Property]
    public ?string $orderDirection = 'ASC';

    #[OA\Property]
    public ?string $code = null;
}
