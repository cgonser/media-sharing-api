<?php

namespace App\User\Request;

use App\Core\Request\SearchRequest;
use OpenApi\Attributes as OA;

#[OA\RequestBody]
class UserFollowSearchRequest extends SearchRequest
{
    #[OA\Property]
    public ?string $followerId = null;

    #[OA\Property]
    public ?string $followingId = null;

    #[OA\Property]
    public ?bool $isPending = null;

    #[OA\Property]
    public ?bool $isApproved = true;
}