<?php

namespace App\User\Request;

use App\Core\Request\AbstractRequest;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\RequestBody]
class UserPasswordResetTokenRequest extends AbstractRequest
{
    #[OA\Property]
    #[Assert\NotBlank]
    public ?string $token = null;

    #[OA\Property]
    #[Assert\NotBlank]
    public ?string $password = null;
}
