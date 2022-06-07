<?php

namespace App\Notification\Service;

use App\Notification\Entity\UserNotificationChannel;
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

        $type = $notification::TYPE;
        $subjectKey = $type.'.subject';
        $contentKey = $type.'.content';

        $notification
            ->setLocale($userLocale)
            ->setTranslator($this->translator)
            ->subject($notification->translate($subjectKey, 'notifications'))
            ->content($notification->translate($contentKey, 'notifications'));

        $this->sendRaw($notification, $user);
    }

    public function sendRaw(AbstractNotification $notification, User $user): void
    {
        $userNotificationChannels = $this->getNotificationChannels($notification, $user);

        $this->doSend($notification, $user, $userNotificationChannels);
    }

    private function doSend(
        AbstractNotification $notification,
        User $user,
        array $userNotificationChannels,
    ): void {
        /** @var UserNotificationChannel $userNotificationChannel */
        foreach ($userNotificationChannels as $userNotificationChannel) {
            $channel = $this->identifyNotificationChannel($userNotificationChannel);

            $channels = [$channel->value];
            $notification->channels($channels);

            $this->notificationManager->create($notification, $user, $channels);
            $this->notifier->send($notification, $this->prepareRecipient($user, $userNotificationChannel));
        }
    }

    private function getNotificationChannels(AbstractNotification $notification, User $user): array
    {
        $userNotificationChannels = $this->userNotificationChannelProvider->findActiveByUser($user->getId());

        /** @var UserNotificationChannel $userNotificationChannel */
        foreach ($userNotificationChannels as $key => $userNotificationChannel) {
            $channel = $this->identifyNotificationChannel($userNotificationChannel);

            if (!in_array($channel, $notification->getAvailableChannels())) {
                unset ($userNotificationChannels[$key]);
            }
        }

        return $userNotificationChannels;
    }

    private function getUserLocale(User $user): string
    {
        return $this->translator->getFallbackLocales()[0];
    }

    private function prepareRecipient(User $user, UserNotificationChannel $userNotificationChannel): Recipient
    {
        $recipient = new Recipient();

        if (NotificationChannel::EMAIL === $userNotificationChannel->getChannel() && null !== $user->getEmail()) {
            $recipient->email($user->getEmail());
        }

        if (NotificationChannel::SMS === $userNotificationChannel->getChannel() && null !== $user->getPhonenumber()) {
            $recipient->phone($user->getPhonenumber());
        }

        if (in_array($userNotificationChannel->getChannel(), [NotificationChannel::PUSH, NotificationChannel::CHAT])) {
            $recipient->userNotificationChannel($userNotificationChannel);
        }

        return $recipient;
    }

    private function identifyNotificationChannel(UserNotificationChannel $userNotificationChannel): NotificationChannel
    {
        return match ($userNotificationChannel->getChannel()) {
            NotificationChannel::PUSH => NotificationChannel::CHAT,
            default => $userNotificationChannel->getChannel(),
        };
    }
}