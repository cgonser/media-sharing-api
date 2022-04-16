<?php

namespace App\Media\Request;

use App\Core\Request\SearchRequest;
use OpenApi\Attributes as OA;

#[OA\RequestBody]
class VideoLikeSearchRequest extends SearchRequest
{
    #[OA\Property]
    public ?string $videoId = null;

    #[OA\Property]
    public ?string $userId = null;
}
