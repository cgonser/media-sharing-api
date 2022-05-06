<?php

namespace App\Media\Request;

use App\Core\Request\AbstractRequest;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\RequestBody]
class LocationRequest extends AbstractRequest
{
    #[OA\Property]
    #[Assert\Type('float')]
    #[Assert\NotBlank]
    public ?float $lat = null;

    #[OA\Property]
    #[Assert\Type('float')]
    #[Assert\NotBlank]
    public ?float $long = null;
}
