<?php

namespace App\Media\Repository;

use App\Core\Repository\BaseRepository;
use App\Media\Entity\VideoMediaItem;
use Doctrine\Persistence\ManagerRegistry;

class VideoMediaItemRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VideoMediaItem::class);
    }
}
