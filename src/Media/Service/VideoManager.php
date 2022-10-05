<?php

namespace App\Media\Service;

use App\Core\Validation\EntityValidator;
use App\Media\Entity\Video;
use App\Media\Entity\VideoMoment;
use App\Media\Enumeration\VideoStatus;
use App\Media\Message\VideoCreatedEvent;
use App\Media\Message\VideoDeletedEvent;
use App\Media\Message\VideoPublishedEvent;
use App\Media\Message\VideoUnpublishedEvent;
use App\Media\Notification\VideoGeneratedNotification;
use App\Media\Notification\VideoPublishedNotification;
use App\Media\Repository\VideoRepository;
use App\Notification\Service\Notifier;
use DateTime;
use Symfony\Component\Messenger\MessageBusInterface;

class VideoManager
{
    public function __construct(
        private readonly VideoRepository $videoRepository,
        private readonly EntityValidator $validator,
        private readonly MessageBusInterface $messageBus,
        private readonly Notifier $notifier,
    ) {
    }

    public function create(Video $video): void
    {
        if (null === $video->getRecordedAt()) {
            $video->setRecordedAt(new \DateTime());
        }

        $this->save($video);

        $this->messageBus->dispatch(new VideoCreatedEvent($video->getId(), $video->getUserId()));
    }

    public function update(Video $video): void
    {
        $this->save($video);
    }

    public function delete(object $video): void
    {
        $this->videoRepository->delete($video);

        $this->messageBus->dispatch(new VideoDeletedEvent($video->getId(), $video->getUserId()));
    }

    public function save(Video $video): void
    {
        $this->validator->validate($video);

        $this->videoRepository->save($video);
    }

    public function defineVideoDuration(Video $video): void
    {
        $video->setDuration(0);

        /** @var VideoMoment $videoMoment */
        foreach ($video->getVideoMoments() as $videoMoment) {
            $video->setDuration($video->getDuration() + $videoMoment->getMoment()->getDuration());
        }
    }

    public function markAsGenerated(Video $video): void
    {
        if (VideoStatus::isGenerated($video->getStatus())) {
            return;
        }

        $video->setStatus(VideoStatus::PREVIEW);

        $this->update($video);

        $this->notifier->send(new VideoGeneratedNotification($video), $video->getUser());
    }

    public function publish(Video $video): void
    {
        if (VideoStatus::PUBLISHED === $video->getStatus()) {
            return;
        }

        $video->setStatus(VideoStatus::PUBLISHED);
        $video->setPublishedAt(new DateTime());

        $this->update($video);

        $this->notifier->send(new VideoPublishedNotification($video), $video->getUser());

        $this->messageBus->dispatch(
            new VideoPublishedEvent(
                $video->getId(),
                $video->getUserId(),
                $video->getPublishedAt(),
            )
        );
    }

    public function unpublish(Video $video): void
    {
        if (VideoStatus::HIDDEN === $video->getStatus()) {
            return;
        }

        $video->setStatus(VideoStatus::HIDDEN);

        $this->update($video);

        $this->messageBus->dispatch(
            new VideoUnpublishedEvent(
                $video->getId(),
                $video->getUserId(),
            )
        );
    }
}
