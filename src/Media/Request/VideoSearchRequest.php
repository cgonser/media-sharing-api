<?php

namespace App\Media\Request;

use App\Core\Request\SearchRequest;
use OpenApi\Attributes as OA;

class VideoSearchRequest extends SearchRequest
{
    public ?string $mood = null;

    public ?string $location = null;

    public ?string $userId = null;

    public ?string $followerId = null;

    public bool $followingOnly = false;
}
