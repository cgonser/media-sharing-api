<?php

namespace App\User\Request;

use App\Core\Request\AbstractRequest;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\RequestBody()
 */
class UserIntegrationRequest extends AbstractRequest
{
    /**
     * @OA\Property()
     * @Assert\NotBlank()
     */
    public ?string $platform;

    /**
     * @OA\Property()
     */
    public ?string $accessToken;

    /**
     * @OA\Property()
     */
    public ?string $externalId;

    /**
     * @OA\Property()
     */
    public ?string $userId;
}
