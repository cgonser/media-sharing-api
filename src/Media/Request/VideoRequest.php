<?php

namespace App\Media\Request;

use App\Core\Request\AbstractRequest;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\RequestBody]
class VideoRequest extends AbstractRequest
{
    #[OA\Property]
    public ?string $userId;

    #[OA\Property]
    public ?string $description;

    #[OA\Property]
    public ?string $mood;

    #[OA\Property]
    public ?string $thumbnail;

    #[OA\Property(type: "array", items: new OA\Items(type: "string"))]
    public ?array $locations;

    #[OA\Property]
    #[Assert\Type('int')]
    public ?int $duration;

    #[OA\Property]
    public ?string $recordedAt;
}
