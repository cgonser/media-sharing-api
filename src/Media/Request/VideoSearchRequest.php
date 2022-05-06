<?php

namespace App\Media\Request;

use App\Core\Request\SearchRequest;
use App\Media\Entity\Video;
use Symfony\Component\Validator\Constraints as Assert;

class VideoSearchRequest extends SearchRequest
{
    #[Assert\Choice(Video::STATUSES)]
    public ?string $status = Video::STATUS_PUBLISHED;

    public ?string $mood = null;

    public ?string $location = null;

    public ?string $userId = null;

    public ?string $followerId = null;

    public bool $followingOnly = false;
}
