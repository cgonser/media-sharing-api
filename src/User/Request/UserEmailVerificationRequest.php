<?php

namespace App\User\Request;

use App\Core\Request\AbstractRequest;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\RequestBody]
class UserEmailVerificationRequest extends AbstractRequest
{
    #[OA\Property]
    #[Assert\Type('uuid')]
    public ?string $userId = null;

    #[OA\Property]
    #[Assert\Type('int')]
    public ?int $code = null;

    #[OA\Property]
    public ?string $token = null;
}
