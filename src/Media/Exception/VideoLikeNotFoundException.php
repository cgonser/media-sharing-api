<?php

namespace App\Media\Exception;

use App\Core\Exception\ResourceNotFoundException;

class VideoLikeNotFoundException extends ResourceNotFoundException
{
    protected $message = 'Like not found';
}
