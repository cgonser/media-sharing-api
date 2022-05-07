<?php

namespace App\Media\Request;

use App\Core\Request\AbstractRequest;
use App\Media\Entity\MediaItem;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\RequestBody]
class MomentMediaItemRequest extends AbstractRequest
{
    #[OA\Property]
    #[Assert\NotBlank]
    #[Assert\Choice(MediaItem::EXTENSIONS)]
    public ?string $extension;

    #[OA\Property]
    #[Assert\NotBlank]
    #[Assert\Choice(MediaItem::TYPES)]
    public ?string $type;
}
