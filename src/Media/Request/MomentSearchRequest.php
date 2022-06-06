<?php

namespace App\Media\Request;

use App\Core\Request\SearchRequest;
use App\Media\Entity\Moment;
use App\Media\Enumeration\MomentStatus;
use App\Media\Enumeration\Mood;
use App\Media\Enumeration\VideoStatus;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\RequestBody]
class MomentSearchRequest extends SearchRequest
{
    public ?VideoStatus $status = null;

    /** @var VideoStatus[]  */
    #[Assert\All(new Assert\Type(VideoStatus::class))]
    #[OA\Property(type: 'array', items: new OA\Items(type: 'string'))]
    public ?array $statuses = null;

    #[Assert\Type(Mood::class)]
    public ?Mood $mood = null;

    #[Assert\AtLeastOneOf([new Assert\Uuid(), new Assert\EqualTo('current')])]
    public ?string $userId = null;

    #[Assert\All(constraints: [new Assert\AtLeastOneOf([new Assert\Uuid(), new Assert\EqualTo('current')])])]
    #[OA\Property(type: 'array', items: new OA\Items(type: 'string'))]
    public ?array $userIdExclusions = [];

    #[Assert\DateTime(format: 'Y-m-d')]
    public ?string $recordedOn = null;

    #[Assert\Type('float')]
    public ?float $longMin = null;

    #[Assert\Type('float')]
    public ?float $longMax = null;

    #[Assert\Type('float')]
    public ?float $latMin = null;

    #[Assert\Type('float')]
    public ?float $latMax = null;

    public ?string $groupBy = null;

    public ?bool $expandMoments = false;
}
