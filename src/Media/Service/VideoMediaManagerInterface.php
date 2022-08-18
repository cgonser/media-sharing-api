<?php

namespace App\Media\Service;

use App\Media\Entity\Video;

interface VideoMediaManagerInterface
{
    public function compose(Video $video): void;
}