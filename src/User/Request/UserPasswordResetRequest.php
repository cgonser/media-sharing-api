<?php

namespace App\User\Request;

use App\Core\Request\AbstractRequest;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\RequestBody(
 *     request="UserPasswordResetRequest"
 * )
 */
class UserPasswordResetRequest extends AbstractRequest
{
    /**
     * @OA\Property()
     * @Assert\NotBlank()
     */
    public ?string $emailAddress = null;
}
