<?php

namespace App\User\Dto;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

class UserIntegrationStatusDto
{
    public ?string $userId;

    /**
     * @OA\Property(type="array", @OA\Items(type="object"))
     */
    public ?array $platforms = [];

    public ?bool $isEmailValidated;
}
