<?php

namespace App\Media\Repository;

use App\Core\Repository\BaseRepository;
use App\Media\Entity\MediaItem;
use Doctrine\Persistence\ManagerRegistry;

class MediaItemRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MediaItem::class);
    }
}
