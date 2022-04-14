<?php

namespace App\User\Dto;

use OpenApi\Attributes as OA;

class UserIntegrationDto
{
    public string $id;

    public ?string $userId;

    public ?string $platform;

    public ?string $externalId;

    #[OA\Property(type: "array", items: new OA\Items(type: "string"))]
    public ?array $details;

    public ?bool $isActive;

    public ?string $accessTokenExpiresAt;

    public ?string $createdAt;

    public ?string $updatedAt;
}
