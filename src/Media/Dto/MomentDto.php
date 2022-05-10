<?php

namespace App\Media\Dto;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class MomentDto
{
    public ?string $id;

    public ?string $userId;

    public ?string $status;

    public ?string $mood;

    public ?LocationDto $location;

    public ?float $duration;

    #[OA\Property(type: "object")]
    public ?array $mediaItems;

    public ?string $recordedOn;

    public ?string $recordedAt;
}
