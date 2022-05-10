<?php

namespace App\Media\Request;

use App\Core\Request\AbstractRequest;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\RequestBody]
class VideoMomentRequest extends AbstractRequest
{
    #[Assert\NotBlank]
    #[Assert\Uuid]
    public string $momentId;

    #[Assert\NotBlank]
    #[Assert\Type('int')]
    public int $position;

    #[Assert\NotBlank]
    #[Assert\Type('float')]
    public float $duration;
}
