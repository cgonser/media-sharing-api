<?php

namespace App\Notification\Service;

use App\Notification\Enumeration\NotificationChannel;
use App\Notification\Notification\AbstractNotification;
use App\User\Entity\User;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Contracts\Translation\TranslatorInterface;

class Notifier
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly NotifierInterface $notifier,
        private readonly NotificationManager $notificationManager,
    ) {
    }

    public function send(AbstractNotification $notification, User $user): void
    {
        $userLocale = $this->getUserLocale($user);
        $channels = $this->getNotificationChannels($notification, $user);
        $recipient = $this->prepareRecipient($user, $channels);

        $type = $notification::TYPE;
        $subjectKey = $type.'.subject';
        $contentKey = $type.'.content';

        $notification->setLocale($userLocale);
        $notification->setTranslator($this->translator);
        $notification->channels($channels);
        $notification->subject($this->translator->trans($subjectKey, [], 'notifications', $userLocale));
        $notification->content($this->translator->trans($contentKey, [], 'notifications', $userLocale));

        $this->notificationManager->create($notification, $user, $channels);

        if (!empty($channels)) {
            $this->notifier->send($notification, $recipient);
        }
    }

    private function getNotificationChannels(AbstractNotification $notification, User $user): array
    {
        $channels = [];

        /** @var NotificationChannel $notificationChannel */
        foreach ($notification->getAvailableChannels() as $notificationChannel) {
            // todo: check if the user opted for receiving notifications in each of these channels
            $channels[] = $notificationChannel->value;
        }

        return $channels;
    }

    private function getUserLocale(User $user): string
    {
        return $this->translator->getFallbackLocales()[0];
    }

    private function prepareRecipient(User $user, array $channels): Recipient
    {
        $recipient = new Recipient(
            $user->getEmail(),
            $user->getPhonenumber() ?? ''
        );

        // TODO: adjust according to the channels

        return $recipient;
    }
}