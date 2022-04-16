<?php

namespace App\Media\Exception;

use App\Core\Exception\ResourceNotFoundException;

class VideoCommentNotFoundException extends ResourceNotFoundException
{
    protected $message = 'Comment not found';
}
