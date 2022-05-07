<?php

namespace App\Media\Provider;

use App\Core\Provider\AbstractProvider;
use App\Media\Repository\MomentMediaItemRepository;

class MomentMediaItemProvider extends AbstractProvider
{
    public function __construct(MomentMediaItemRepository $repository)
    {
        $this->repository = $repository;
    }
}
