<?php

namespace App\Media\Service;

use App\Core\Validation\EntityValidator;
use App\Media\Entity\Video;
use App\Media\Entity\VideoComment;
use App\Media\Notification\VideoCommentedNotification;
use App\Media\Repository\VideoCommentRepository;
use App\Notification\Service\Notifier;
use App\User\Entity\User;

class VideoCommentManager
{
    public function __construct(
        private readonly VideoCommentRepository $videoCommentRepository,
        private readonly EntityValidator $validator,
        private readonly Notifier $notifier,
    ) {
    }

    public function create(Video $video, User $user, string $comment): VideoComment
    {
        $videoComment = (new VideoComment())
            ->setVideo($video)
            ->setUser($user)
            ->setComment($comment)
        ;

        $this->save($videoComment);

        $this->notifier->send(new VideoCommentedNotification($videoComment), $videoComment->getUser());

        return $videoComment;
    }

    public function update(VideoComment $videoComment, string $comment): void
    {
        $videoComment->setComment($comment);

        $this->save($videoComment);
    }

    public function save(VideoComment $videoComment): void
    {
        $this->validator->validate($videoComment);

        $this->videoCommentRepository->save($videoComment);
    }

    public function delete(VideoComment $videoComment): void
    {
        $this->videoCommentRepository->delete($videoComment);
    }
}
