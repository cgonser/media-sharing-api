<?php

namespace App\Media\Request;

use App\Core\Request\AbstractRequest;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\RequestBody]
class LocationRequest extends AbstractRequest
{
    #[Assert\Type('float')]
    #[Assert\NotBlank]
    public ?float $lat;

    #[Assert\Type('float')]
    #[Assert\NotBlank]
    public ?float $long;

    #[Assert\NotBlank]
    public ?string $googlePlaceId;

    public ?string $address;
}
