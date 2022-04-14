<?php

namespace App\User\Request;

use App\Core\Request\SearchRequest;
use OpenApi\Attributes as OA;

#[OA\RequestBody]
class UserActivitySearchRequest extends SearchRequest
{
    #[OA\Property]
    public ?string $userId = null;

    #[OA\Property]
    public ?string $startsAt = null;

    #[OA\Property]
    public ?string $endsAt = null;
}
