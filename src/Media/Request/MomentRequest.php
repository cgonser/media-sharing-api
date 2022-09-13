<?php

namespace App\Media\Request;

use App\Core\Request\AbstractRequest;
use App\Media\Enumeration\Mood;
use DateTimeInterface;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\RequestBody]
class MomentRequest extends AbstractRequest
{
    public ?string $userId;

    public ?string $localPath;

    #[Assert\Type(Mood::class)]
    public ?Mood $mood;

    #[Assert\Valid]
    public ?LocationRequest $location;

    public ?int $duration;

    #[Assert\DateTime(format: DateTimeInterface::ATOM)]
    public ?string $recordedAt;
}
