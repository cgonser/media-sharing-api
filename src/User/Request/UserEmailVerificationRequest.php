<?php

namespace App\User\Request;

use App\Core\Request\AbstractRequest;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\RequestBody()
 */
class UserEmailVerificationRequest extends AbstractRequest
{
    /**
     * @OA\Property()
     * @Assert\NotBlank()
     */
    public ?string $token = null;
}
