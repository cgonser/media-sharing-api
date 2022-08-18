<?php

namespace App\Tests\Classes\Media\Service;

use App\Media\Entity\Video;
use App\Media\Service\VideoMediaManagerInterface;

class VideoMediaManager implements VideoMediaManagerInterface
{
    public function compose(Video $video): void
    {
    }
}