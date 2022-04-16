<?php

namespace App\Media\Provider;

use App\Core\Provider\AbstractProvider;
use App\Media\Entity\VideoLike;
use App\Media\Exception\VideoLikeNotFoundException;
use App\Media\Repository\VideoLikeRepository;
use Ramsey\Uuid\UuidInterface;

class VideoLikeProvider extends AbstractProvider
{
    public function __construct(VideoLikeRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getByVideoAndUserId(UuidInterface $videoId, UuidInterface $userId): VideoLike
    {
        /** @var VideoLike|null $videoLike */
        $videoLike = $this->repository->findOneBy([
            'videoId' => $videoId,
            'userId' => $userId,
        ]);

        if (!$videoLike) {
            throw new VideoLikeNotFoundException();
        }

        return $videoLike;
    }

    protected function throwNotFoundException(): void
    {
        throw new VideoLikeNotFoundException();
    }

    protected function getFilterableFields(): array
    {
        return [
            'videoId',
            'userId',
        ];
    }
}
