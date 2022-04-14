<?php

namespace App\User\Request;

use App\Core\Request\AbstractRequest;
use OpenApi\Attributes as OA;

#[OA\RequestBody]
class UserSettingRequest extends AbstractRequest
{
    #[OA\Property]
    public ?string $value;
}