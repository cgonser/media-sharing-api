<?php

namespace App\Localization\Request;

use App\Core\Request\SearchRequest;
use OpenApi\Annotations as OA;

/**
 * @OA\RequestBody()
 */
class LanguageSearchRequest extends SearchRequest
{
    /**
     * @OA\Property()
     */
    public ?string $orderProperty = 'name';

    /**
     * @OA\Property()
     */
    public ?string $orderDirection = 'ASC';

    /**
     * @OA\Property()
     */
    public ?string $code = null;
}
