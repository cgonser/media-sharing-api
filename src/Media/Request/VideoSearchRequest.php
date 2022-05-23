<?php

namespace App\Media\Request;

use App\Core\Request\SearchRequest;
use App\Media\Enumeration\Mood;
use App\Media\Enumeration\VideoStatus;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\RequestBody]
class VideoSearchRequest extends SearchRequest
{
    public ?VideoStatus $status = null;

    /** @var VideoStatus[]  */
    #[Assert\All(new Assert\Type(VideoStatus::class))]
    #[OA\Property(type: 'array', items: new OA\Items(type: 'string'))]
    public ?array $statuses = null;

    #[Assert\Type(Mood::class)]
    public ?Mood $mood = null;

    /** @var Mood[]  */
    #[Assert\All(new Assert\Type(Mood::class))]
    #[OA\Property(type: 'array', items: new OA\Items(type: 'string'))]
    public ?array $moods = null;

    public ?string $userId = null;

    public ?string $followerId = null;

    public bool $followingOnly = false;
}
