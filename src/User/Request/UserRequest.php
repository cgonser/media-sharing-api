<?php

namespace App\User\Request;

use App\Core\Request\AbstractRequest;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\RequestBody]
class UserRequest extends AbstractRequest
{
    #[OA\Property]
    public ?string $name;

    #[OA\Property]
    #[Assert\Email]
    public ?string $email;

    #[OA\Property]
    public ?string $password;

    #[OA\Property]
    public ?string $profilePicture;

    #[OA\Property]
    public ?string $country;

    #[OA\Property]
    public ?string $locale;

    #[OA\Property]
    public ?string $timezone;

    #[OA\Property]
    public ?string $currencyCode;

    #[OA\Property]
    public ?bool $allowEmailMarketing;

    #[OA\Property]
    public ?bool $isProfilePrivate;

    #[OA\Property]
    public ?bool $isActive;
}
