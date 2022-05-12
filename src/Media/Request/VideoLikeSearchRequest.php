<?php

namespace App\Media\Request;

use App\Core\Request\SearchRequest;
use OpenApi\Attributes as OA;

#[OA\RequestBody]
class VideoLikeSearchRequest extends SearchRequest
{
    public ?string $videoId = null;

    public ?string $userId = null;
}
