<?php

namespace App\Media\Service;

use App\Core\Validation\EntityValidator;
use App\Media\Entity\Video;
use App\Media\Entity\VideoLike;
use App\Media\Notification\VideoLikedNotification;
use App\Media\Provider\VideoLikeProvider;
use App\Media\Repository\VideoLikeRepository;
use App\Notification\Service\Notifier;
use App\User\Entity\User;

class VideoLikeManager
{
    public function __construct(
        private readonly VideoLikeRepository $videoLikeRepository,
        private readonly VideoLikeProvider $videoLikeProvider,
        private readonly EntityValidator $validator,
        private readonly Notifier $notifier,
    ) {
    }

    public function like(Video $video, User $user): void
    {
        $videoLike = (new VideoLike())
            ->setVideo($video)
            ->setUser($user);

        $this->save($videoLike);

        $this->notifier->send(new VideoLikedNotification($videoLike), $videoLike->getUser());
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
