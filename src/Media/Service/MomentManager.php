<?php

namespace App\Media\Service;

use App\Core\Validation\EntityValidator;
use App\Media\Entity\Moment;
use App\Media\Enumeration\MomentStatus;
use App\Media\Message\MomentPublishedEvent;
use App\Media\Notification\MomentPublishedNotification;
use App\Media\Repository\MomentRepository;
use App\Notification\Service\Notifier;
use DateTime;
use Symfony\Component\Messenger\MessageBusInterface;

class MomentManager
{
    public function __construct(
        private readonly MomentRepository $momentRepository,
        private readonly EntityValidator $validator,
        private readonly MessageBusInterface $messageBus,
        private readonly Notifier $notifier,
    ) {
    }

    public function create(Moment $moment): void
    {
        $this->save($moment);
    }

    public function update(Moment $moment): void
    {
        $this->save($moment);
    }

    public function delete(object $moment): void
    {
        $this->momentRepository->delete($moment);
    }

    public function save(Moment $moment): void
    {
        $this->validator->validate($moment);

        $this->momentRepository->save($moment);
    }

    public function publish(Moment $moment, ?int $duration = null): void
    {
        $moment->setDuration($duration);
        $moment->setStatus(MomentStatus::PUBLISHED);
        $moment->setPublishedAt(new DateTime());

        $this->update($moment);

        $this->notifier->send(new MomentPublishedNotification($moment), $moment->getUser());

        $this->messageBus->dispatch(
            new MomentPublishedEvent(
                $moment->getId(),
                $moment->getUserId(),
                $moment->getPublishedAt(),
            )
        );
    }
}
