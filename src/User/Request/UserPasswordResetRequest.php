<?php

namespace App\User\Request;

use App\Core\Request\AbstractRequest;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\RequestBody]
class UserPasswordResetRequest extends AbstractRequest
{
    #[OA\Property]
    #[Assert\NotBlank]
    public ?string $emailAddress = null;
}
