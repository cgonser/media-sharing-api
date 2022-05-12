<?php

namespace App\Media\Service;

use App\Core\Validation\EntityValidator;
use App\Media\Entity\Video;
use App\Media\Enumeration\VideoStatus;
use App\Media\Message\VideoPublishedEvent;
use App\Media\Repository\VideoRepository;
use DateTime;
use Symfony\Component\Messenger\MessageBusInterface;

class VideoManager
{
    public function __construct(
        private readonly VideoRepository $videoRepository,
        private readonly EntityValidator $validator,
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    public function create(Video $video): void
    {
        $this->save($video);
    }

    public function update(Video $video): void
    {
        $this->save($video);
    }

    public function delete(object $video): void
    {
        $this->videoRepository->delete($video);
    }

    public function save(Video $video): void
    {
        $this->validator->validate($video);

        $this->videoRepository->save($video);
    }

    public function publish(Video $video): void
    {
        $video->setStatus(VideoStatus::PUBLISHED);
        $video->setPublishedAt(new DateTime());

        $this->update($video);

        $this->messageBus->dispatch(
            new VideoPublishedEvent(
                $video->getId(),
                $video->getUserId(),
                $video->getPublishedAt(),
            )
        );
    }
}
