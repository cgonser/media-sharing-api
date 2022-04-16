<?php

namespace App\Media\Request;

use App\Core\Request\SearchRequest;
use OpenApi\Attributes as OA;

#[OA\RequestBody]
class VideoSearchRequest extends SearchRequest
{
    #[OA\Property]
    public ?string $mood = null;

    #[OA\Property]
    public ?string $location = null;

    #[OA\Property]
    public ?string $userId = null;

    #[OA\Property]
    public ?string $followerId = null;
}
