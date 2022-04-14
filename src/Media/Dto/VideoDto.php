<?php

namespace App\Media\Dto;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class VideoDto
{
    public ?string $id;

    public ?string $userId;

    public ?string $description;

    public ?string $thumbnail;

    public ?string $mood;

    #[OA\Property(type: "array", items: new OA\Items(type: "string"))]
    public ?array $locations;

    #[OA\Property(type: "array", items: new OA\Items(ref: new Model(type: MomentDto::class)))]
    public ?array $moments;

    public ?int $duration;

    public ?int $likes;

    public ?int $comments;

    public ?string $recordedAt;
}
