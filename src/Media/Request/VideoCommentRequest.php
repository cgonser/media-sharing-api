<?php

namespace App\Media\Request;

use App\Core\Request\AbstractRequest;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\RequestBody]
class VideoCommentRequest extends AbstractRequest
{
    #[OA\Property]
    #[Assert\NotBlank]
    public ?string $comment;
}
