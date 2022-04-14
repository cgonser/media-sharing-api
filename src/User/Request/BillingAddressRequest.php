<?php

namespace App\User\Request;

use App\Core\Request\AbstractRequest;
use OpenApi\Attributes as OA;

#[OA\RequestBody]
class BillingAddressRequest extends AbstractRequest
{
    #[OA\Property]
    public ?string $userId;

    #[OA\Property]
    public ?string $companyName;

    #[OA\Property]
    public ?string $name;

    #[OA\Property]
    public ?string $email;

    #[OA\Property]
    public ?string $birthDate;

    #[OA\Property]
    public ?string $phoneIntlCode;

    #[OA\Property]
    public ?string $phoneAreaCode;

    #[OA\Property]
    public ?string $phoneNumber;

    #[OA\Property]
    public ?string $documentType;

    #[OA\Property]
    public ?string $documentNumber;

    #[OA\Property]
    public ?string $addressLine1;

    #[OA\Property]
    public ?string $addressLine2;

    #[OA\Property]
    public ?string $addressNumber;

    #[OA\Property]
    public ?string $addressDistrict;

    #[OA\Property]
    public ?string $addressCity;

    #[OA\Property]
    public ?string $addressState;

    #[OA\Property]
    public ?string $addressCountry;

    #[OA\Property]
    public ?string $addressZipCode;
}
