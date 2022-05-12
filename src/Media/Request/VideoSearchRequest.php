<?php

namespace App\Media\Request;

use App\Core\Request\SearchRequest;
use App\Media\Enumeration\Mood;
use App\Media\Enumeration\VideoStatus;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class VideoSearchRequest extends SearchRequest
{
    public VideoStatus $status = VideoStatus::PUBLISHED;

    #[Assert\Type(Mood::class)]
    public ?Mood $mood = null;

    /** @var array<Mood>  */
    #[Assert\All(new Assert\Type(Mood::class))]
    #[OA\Property(type: 'array', items: new OA\Items(type: 'string'))]
    public ?array $moods = null;

    public ?string $location = null;

    public ?string $userId = null;

    public ?string $followerId = null;

    public bool $followingOnly = false;
}
