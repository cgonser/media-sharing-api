<?php

namespace App\Media\Request;

use App\Core\Request\AbstractRequest;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\RequestBody]
class VideoMomentRequest extends AbstractRequest
{
    #[OA\Property]
    #[Assert\NotBlank]
    #[Assert\Uuid]
    public string $momentId;

    #[OA\Property]
    #[Assert\NotBlank]
    #[Assert\Type('int')]
    public int $position;
}
