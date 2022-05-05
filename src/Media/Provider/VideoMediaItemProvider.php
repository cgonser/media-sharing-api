<?php

namespace App\Media\Provider;

use App\Core\Provider\AbstractProvider;
use App\Media\Repository\VideoMediaItemRepository;

class VideoMediaItemProvider extends AbstractProvider
{
    public function __construct(VideoMediaItemRepository $repository)
    {
        $this->repository = $repository;
    }
}
