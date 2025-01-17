<?php

namespace App\Media\Request;

use App\Core\Request\SearchRequest;
use App\Media\Enumeration\Mood;
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

    public ?string $userId = null;

    #[Assert\All(constraints: [new Assert\AtLeastOneOf([new Assert\Uuid(), new Assert\EqualTo('current')])])]
    #[OA\Property(type: 'array', items: new OA\Items(type: 'string'))]
    public ?array $userIdExclusions = [];

    #[Assert\Type(Mood::class)]
    public ?Mood $mood = null;
}
