<?php

namespace App\User\Request;

use App\Core\Request\SearchRequest;
use OpenApi\Attributes as OA;

#[OA\RequestBody]
class UserSettingSearchRequest extends SearchRequest
{
    #[OA\Property]
    public ?string $userId = null;
}