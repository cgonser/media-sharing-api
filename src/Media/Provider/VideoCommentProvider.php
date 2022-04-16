<?php

namespace App\Media\Provider;

use App\Core\Provider\AbstractProvider;
use App\Media\Entity\VideoComment;
use App\Media\Exception\VideoCommentNotFoundException;
use App\Media\Repository\VideoCommentRepository;
use Ramsey\Uuid\UuidInterface;

class VideoCommentProvider extends AbstractProvider
{
    public function __construct(VideoCommentRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getByVideoAndId(UuidInterface $videoId, UuidInterface $id): VideoComment
    {
        /** @var VideoComment|null $videoComment */
        $videoComment = $this->repository->findOneBy([
            'id' => $id,
            'videoId' => $videoId,
        ]);

        if (!$videoComment) {
            throw new VideoCommentNotFoundException();
        }

        return $videoComment;
    }

    protected function throwNotFoundException(): void
    {
        throw new VideoCommentNotFoundException();
    }

    protected function getFilterableFields(): array
    {
        return [
            'videoId',
        ];
    }
}
