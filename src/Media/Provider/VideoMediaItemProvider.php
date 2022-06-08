<?php

namespace App\Media\Provider;

use App\Core\Provider\AbstractProvider;
use App\Media\Entity\VideoMediaItem;
use App\Media\Enumeration\MediaItemType;
use App\Media\Repository\VideoMediaItemRepository;
use Ramsey\Uuid\UuidInterface;

class VideoMediaItemProvider extends AbstractProvider
{
    public function __construct(VideoMediaItemRepository $repository)
    {
        $this->repository = $repository;
    }

    public function findOneByVideoAndType(UuidInterface $videoId, MediaItemType $mediaItemType): ?VideoMediaItem
    {
        return $this->repository->createQueryBuilder('vmi')
            ->innerJoin('vmi.mediaItem', 'mi')
            ->where('vmi.videoId = :videoId')
            ->andWhere('mi.type = :type')
            ->setParameters([
                'videoId' => $videoId,
                'type' => $mediaItemType->value,
            ])
            ->getQuery()
            ->useQueryCache(true)
            ->getOneOrNullResult();
    }
}
