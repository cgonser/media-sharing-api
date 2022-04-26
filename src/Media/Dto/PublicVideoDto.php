<?php

namespace App\Media\Dto;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class PublicVideoDto
{
    public ?string $id;

    public ?string $userId;

    public ?string $description;

    public ?string $thumbnail;

    #[OA\Property(type: "array", items: new OA\Items(type: "string"))]
    public ?array $moods;

    #[OA\Property(type: "array", items: new OA\Items(type: "string"))]
    public ?array $locations;

    #[OA\Property(type: "array", items: new OA\Items(ref: new Model(type: MomentDto::class)))]
    public ?array $moments;

    #[OA\Property(type: "array", items: new OA\Items(ref: new Model(type: PublicMediaItemDto::class)))]
    public ?array $mediaItems;

    public ?int $duration;

    public ?int $likes;

    public ?int $comments;

    public ?string $recordedAt;
}