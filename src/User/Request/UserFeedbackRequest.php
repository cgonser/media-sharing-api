<?php

namespace App\User\Request;

use App\Core\Request\AbstractRequest;
use OpenApi\Attributes as OA;

#[OA\RequestBody]
class UserFeedbackRequest extends AbstractRequest
{
    public ?string $type = null;

    public ?string $screen = null;

    public ?string $description = null;

    #[OA\Property(description: "Attachment contents encoded with base64")]
    public ?string $attachmentContents = null;

    public ?string $attachmentFilename = null;
}
