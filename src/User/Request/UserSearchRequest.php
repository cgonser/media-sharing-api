<?php

namespace App\User\Request;

use App\Core\Request\SearchRequest;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\RequestBody]
class UserSearchRequest extends SearchRequest
{
    public ?string $userId = null;

    public ?string $username = null;

    public ?bool $excludeCurrent = true;

    #[Assert\All(constraints: [new Assert\Uuid()])]
    #[OA\Property(type: 'array', items: new OA\Items(type: 'string'))]
    public ?array $exclusions = [];
}
