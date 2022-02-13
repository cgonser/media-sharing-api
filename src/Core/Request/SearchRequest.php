<?php

namespace App\Core\Request;

use OpenApi\Annotations as OA;

/**
 * @OA\RequestBody(
 *     request="SearchRequest"
 * )
 */
class SearchRequest
{
    /**
     * @OA\Property()
     */
    public ?string $search = null;

    /**
     * @OA\Property()
     */
    public ?int $page = 1;

    /**
     * @OA\Property()
     */
    public ?int $resultsPerPage = 100;

    /**
     * @OA\Property()
     */
    public ?string $orderProperty = null;

    /**
     * @OA\Property()
     */
    public ?string $orderDirection = null;
}
