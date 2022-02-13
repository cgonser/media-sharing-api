<?php

namespace App\User\Request;

use App\Core\Request\AbstractRequest;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\RequestBody(
 *     request="UserPasswordChangeRequest",
 *     required={"email", "password"},
 * )
 */
class UserPasswordChangeRequest extends AbstractRequest
{
    /**
     * @OA\Property()
     * @Assert\NotBlank()
     */
    public ?string $currentPassword = null;

    /**
     * @OA\Property()
     * @Assert\NotBlank()
     */
    public ?string $newPassword = null;
}
