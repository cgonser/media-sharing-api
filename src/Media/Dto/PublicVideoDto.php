<?php

namespace App\Media\Dto;

use App\User\Dto\PublicUserDto;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class PublicVideoDto
{
    public ?string $id;

    public ?string $userId;

    public ?PublicUserDto $user;

    public ?string $description;

    #[OA\Property(type: "array", items: new OA\Items(type: "string"))]
    public ?array $moods;

    #[OA\Property(type: "array", items: new OA\Items(type: "string"))]
    public ?array $locations;

    #[OA\Property(type: "array", items: new OA\Items(ref: new Model(type: MomentDto::class)))]
    public ?array $moments;

    #[OA\Property(type: "array", items: new OA\Items(type: "object"))]
    public ?array $mediaItems;

    public ?int $duration;

    public ?int $likes;

    public ?int $comments;

    public ?string $recordedAt;
}
