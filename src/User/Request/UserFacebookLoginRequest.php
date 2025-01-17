<?php

namespace App\User\Request;

use App\Core\Request\AbstractRequest;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\RequestBody]
class UserFacebookLoginRequest extends AbstractRequest
{
    #[OA\Property]
    #[Assert\NotBlank]
    public ?string $accessToken = null;

    #[OA\Property]
    public ?string $userId = null;
}
