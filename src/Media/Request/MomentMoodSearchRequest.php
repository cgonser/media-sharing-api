<?php

namespace App\Media\Request;

use App\Core\Request\SearchRequest;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\RequestBody]
class MomentMoodSearchRequest extends SearchRequest
{
    #[Assert\NotBlank]
    public float $longMin;

    #[Assert\NotBlank]
    public float $longMax;

    #[Assert\NotBlank]
    public float $latMin;

    #[Assert\NotBlank]
    public float $latMax;
}
