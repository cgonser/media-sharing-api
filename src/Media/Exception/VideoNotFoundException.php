<?php

namespace App\Media\Exception;

use App\Core\Exception\ResourceNotFoundException;

class VideoNotFoundException extends ResourceNotFoundException
{
    protected $message = 'Video not found';
}
