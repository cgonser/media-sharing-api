<?php

namespace App\Media\Exception;

use App\Core\Exception\InvalidInputException;

class InvalidMediaItemExtensionException extends InvalidInputException
{
    protected $message = 'media_item.invalid_extension';
}