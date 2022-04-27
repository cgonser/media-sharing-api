<?php

namespace App\Media\Dto;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class MomentDto
{
    public ?string $id;

    public ?string $userId;

    public ?string $mood;

    public ?string $location;

    public ?int $duration;

    #[OA\Property(type: "array", items: new OA\Items(ref: new Model(type: MediaItemDto::class)))]
    public ?array $mediaItems;

    public ?string $recordedOn;

    public ?string $recordedAt;
}
