<?php

namespace App\Media\Request;

use App\Core\Request\SearchRequest;
use OpenApi\Attributes as OA;

#[OA\RequestBody]
class MusicSearchRequest extends SearchRequest
{
    public bool $isActive = true;
}
