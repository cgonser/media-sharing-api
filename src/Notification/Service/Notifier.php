<?php

namespace App\Notification\Service;

use App\Notification\Enumeration\NotificationChannel;
use App\Notification\Notification\AbstractNotification;
use App\Notification\Provider\UserNotificationChannelProvider;
use App\Notification\Recipient\Recipient;
use App\User\Entity\User;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class Notifier
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly UserNotificationChannelProvider $userNotificationChannelProvider,
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
        $recipient = new Recipient();

        if (in_array(NotificationChannel::EMAIL->value, $channels) && null !== $user->getEmail()) {
            $recipient->email($user->getEmail());
        }

        if (in_array(NotificationChannel::SMS->value, $channels) && null !== $user->getPhonenumber()) {
            $recipient->phone($user->getPhonenumber());
        }

        if (in_array(NotificationChannel::PUSH->value, $channels) || in_array(NotificationChannel::CHAT->value, $channels)) {
            $userNotificationChannel = $this->userNotificationChannelProvider->findOneByUserAndChannel(
                $user->getId(),
                NotificationChannel::PUSH
            );

            if ($userNotificationChannel) {
                $recipient->pushSettings($userNotificationChannel->getDetails());
            }
        }

        return $recipient;
    }
}