<?php

namespace App\Media\Provider;

use App\Core\Provider\AbstractProvider;
use App\Media\Entity\MediaItem;
use App\Media\Repository\MediaItemRepository;

class MediaItemProvider extends AbstractProvider
{
    public function __construct(MediaItemRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getByFilename(string $filename): MediaItem
    {
        return $this->getBy([
            'filename' => $filename,
        ]);
    }
}
