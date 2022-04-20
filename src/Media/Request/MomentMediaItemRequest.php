<?php

namespace App\Media\Request;

use App\Core\Request\AbstractRequest;
use OpenApi\Attributes as OA;

#[OA\RequestBody]
class MomentMediaItemRequest extends AbstractRequest
{
    #[OA\Property]
    public ?string $extension;
}
