<?php

namespace App\Media\Provider;

use App\Core\Provider\AbstractProvider;
use App\Media\Repository\MusicRepository;

class MusicProvider extends AbstractProvider
{
    public function __construct(MusicRepository $repository)
    {
        $this->repository = $repository;
    }

    protected function getFilterableFields(): array
    {
        return [
            'isActive',
        ];
    }
}
