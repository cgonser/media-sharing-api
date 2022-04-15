<?php

namespace App\Media\Exception;

use App\Core\Exception\ResourceNotFoundException;

class MomentNotFoundException extends ResourceNotFoundException
{
    protected $message = 'Moment not found';
}
