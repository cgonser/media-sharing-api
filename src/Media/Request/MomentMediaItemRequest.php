<?php

namespace App\Media\Request;

use App\Core\Request\AbstractRequest;
use App\Media\Enumeration\MediaItemExtension;
use App\Media\Enumeration\MediaItemType;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\RequestBody]
class MomentMediaItemRequest extends AbstractRequest
{
    #[Assert\NotBlank]
    #[Assert\Type(MediaItemExtension::class)]
    public MediaItemExtension $extension;

    public ?string $type;
}
