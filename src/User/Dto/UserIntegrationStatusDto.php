<?php

namespace App\User\Dto;

use OpenApi\Attributes as OA;

class UserIntegrationStatusDto
{
    public ?string $userId;

    #[OA\Property(type: "array", items: new OA\Items(type: "object"))]
    public ?array $platforms = [];

    public ?bool $isEmailValidated;
}
