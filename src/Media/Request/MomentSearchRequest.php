<?php

namespace App\Media\Request;

use App\Core\Request\SearchRequest;
use App\Media\Entity\Moment;
use App\Media\Enumeration\MomentStatus;
use App\Media\Enumeration\Mood;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\RequestBody]
class MomentSearchRequest extends SearchRequest
{
    #[Assert\Type(MomentStatus::class)]
    public MomentStatus $status = MomentStatus::PUBLISHED;

    #[Assert\Type(Mood::class)]
    public ?Mood $mood = null;

    public ?string $userId = null;

    #[Assert\DateTime(format: 'Y-m-d')]
    public ?string $recordedOn = null;

    public ?string $groupBy = null;

    public ?bool $expandMoments = false;
}
