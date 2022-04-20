<?php

namespace App\Media\Repository;

use App\Core\Repository\BaseRepository;
use App\Media\Entity\MomentMediaItem;
use Doctrine\Persistence\ManagerRegistry;

class MomentMediaItemRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MomentMediaItem::class);
    }
}
