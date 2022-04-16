<?php

namespace App\Media\Service;

use App\Core\Validation\EntityValidator;
use App\Media\Entity\Video;
use App\Media\Entity\VideoLike;
use App\Media\Provider\VideoLikeProvider;
use App\Media\Repository\VideoLikeRepository;
use App\User\Entity\User;

class VideoLikeManager
{
    public function __construct(
        private VideoLikeRepository $videoLikeRepository,
        private VideoLikeProvider $videoLikeProvider,
        private EntityValidator $validator,
    ) {
    }

    public function like(Video $video, User $user): void
    {
        $videoLike = (new VideoLike())
            ->setVideo($video)
            ->setUser($user);

        $this->save($videoLike);
    }

    public function unlike(Video $video, User $user): void
    {
        $this->videoLikeRepository->delete(
            $this->videoLikeProvider->getByVideoAndUserId($video->getId(), $user->getId())
        );
    }

    public function save(VideoLike $videoLike): void
    {
        $this->validator->validate($videoLike);

        $this->videoLikeRepository->save($videoLike);
    }
}
