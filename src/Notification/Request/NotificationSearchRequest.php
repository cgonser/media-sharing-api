<?php

namespace App\Notification\Request;

use App\Core\Request\SearchRequest;
use OpenApi\Attributes as OA;

#[OA\RequestBody]
class NotificationSearchRequest extends SearchRequest
{
    public ?string $userId = null;

    public ?bool $isNew = true;

    #[OA\Property]
    public ?string $orderProperty = 'createdAt';

    #[OA\Property]
    public ?string $orderDirection = 'DESC';
}
