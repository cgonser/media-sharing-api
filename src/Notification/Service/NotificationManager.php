<?php

namespace App\Notification\Service;

use App\Core\Validation\EntityValidator;
use App\Notification\Entity\Notification;
use App\Notification\Notification\AbstractNotification;
use App\Notification\Repository\NotificationRepository;
use App\User\Entity\User;

class NotificationManager
{
    public function __construct(
        private readonly NotificationRepository $notificationRepository,
        private readonly EntityValidator $validator,
    ) {
    }

    public function create(AbstractNotification $notification, User $user, array $channels): Notification
    {
        $entity = (new Notification())
            ->setType($notification::TYPE)
            ->setUser($user)
            ->setContent($notification->getContent())
            ->setContext($notification->getContext())
            ->setChannels($channels)
        ;

        $this->save($entity);

        return $entity;
    }

    public function save(Notification $notification): void
    {
        $this->validator->validate($notification);

        $this->notificationRepository->save($notification);
    }
}