<?php

namespace App\Media\Enumeration;

enum VideoStatus: string
{
    case PENDING = 'pending';
    case PUBLISHED = 'published';
    case HIDDEN = 'hidden';
}