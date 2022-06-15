<?php

namespace App\Media\Request;

use App\Core\Request\AbstractRequest;
use App\Media\Enumeration\Mood;
use DateTimeInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\RequestBody]
class VideoRequest extends AbstractRequest
{
    public ?string $userId;

    public ?string $description;

    /** @var Mood[]  */
    #[Assert\All(new Assert\Type(Mood::class))]
    #[OA\Property(type: "array", items: new OA\Items(type: 'string'))]
    public ?array $moods;

    /** @var LocationRequest[] */
    #[OA\Property(type: "array", items: new OA\Items(ref: new Model(type: LocationRequest::class)))]
    #[Assert\All([new Assert\Type(type: LocationRequest::class)])]
    #[Assert\Valid]
    public ?array $locations;

    /** @var VideoMomentRequest[] */
    #[OA\Property(type: "array", items: new OA\Items(ref: new Model(type: VideoMomentRequest::class)))]
    #[Assert\All([new Assert\Type(type: VideoMomentRequest::class)])]
    #[Assert\Valid]
    public ?array $moments;

    #[Assert\Uuid]
    public ?string $musicId;

    public ?bool $overrideMomentsAudio;

    #[Assert\DateTime(format: DateTimeInterface::ATOM)]
    public ?string $recordedAt;

    // todo: remove it
    public ?int $duration;
}
