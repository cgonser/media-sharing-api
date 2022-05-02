<?php

namespace App\Media\Request;

use App\Core\Request\SearchRequest;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\RequestBody]
class MomentSearchRequest extends SearchRequest
{
    #[OA\Property]
    public ?string $mood = null;

    #[OA\Property]
    public ?string $location = null;

    #[OA\Property]
    public ?string $userId = null;

    #[OA\Property]
    #[Assert\DateTime(format: 'Y-m-d')]
    public ?string $recordedOn = null;

    #[OA\Property]
    public ?string $groupBy = null;
}
