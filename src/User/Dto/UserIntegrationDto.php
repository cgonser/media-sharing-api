<?php

namespace App\User\Dto;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

class UserIntegrationDto
{
    public string $id;

    public ?string $userId;

    public ?string $platform;

    public ?string $externalId;

    /**
     * @OA\Property(type="array", @OA\Items(type="string"))
     */
    public ?array $details;

    public ?bool $isActive;

    public ?string $accessTokenExpiresAt;

    public ?string $createdAt;

    public ?string $updatedAt;
}
