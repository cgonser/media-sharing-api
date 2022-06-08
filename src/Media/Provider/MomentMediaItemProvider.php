<?php

namespace App\Media\Provider;

use App\Core\Provider\AbstractProvider;
use App\Media\Entity\MomentMediaItem;
use App\Media\Enumeration\MediaItemType;
use App\Media\Repository\MomentMediaItemRepository;
use Ramsey\Uuid\UuidInterface;

class MomentMediaItemProvider extends AbstractProvider
{
    public function __construct(MomentMediaItemRepository $repository)
    {
        $this->repository = $repository;
    }

    public function findOneByMomentAndType(UuidInterface $momentId, MediaItemType $mediaItemType): ?MomentMediaItem
    {
        return $this->repository->createQueryBuilder('mmi')
            ->innerJoin('mmi.mediaItem', 'mi')
            ->where('mmi.momentId = :momentId')
            ->andWhere('mi.type = :type')
            ->setParameters([
                'momentId' => $momentId,
                'type' => $mediaItemType->value,
            ])
            ->getQuery()
            ->useQueryCache(true)
            ->getOneOrNullResult();
    }
}
