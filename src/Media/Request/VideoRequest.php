<?php

namespace App\Media\Request;

use App\Core\Request\AbstractRequest;
use DateTimeInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\RequestBody]
class VideoRequest extends AbstractRequest
{
    #[OA\Property]
    public ?string $userId;

    #[OA\Property]
    public ?string $description;

    #[OA\Property(type: "array", items: new OA\Items(type: 'string'))]
    public ?array $moods;

    #[OA\Property(type: "array", items: new OA\Items(type: 'string'))]
    public ?array $locations;

    /** @var VideoMomentRequest[] */
    #[OA\Property(type: "array", items: new OA\Items(ref: new Model(type: VideoMomentRequest::class)))]
    #[Assert\All([new Assert\Type(type: VideoMomentRequest::class)])]
    #[Assert\Valid]
    public ?array $moments;

    #[OA\Property]
    #[Assert\Type('int')]
    public ?int $duration;

    #[OA\Property]
    #[Assert\DateTime(format: DateTimeInterface::ATOM)]
    public ?string $recordedAt;
}
