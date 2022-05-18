<?php

namespace App\Notification\Service;

use App\Notification\Entity\UserNotificationChannel;
use App\Notification\Provider\UserNotificationChannelProvider;
use App\Notification\Request\UserNotificationChannelRequest;
use App\User\Provider\UserProvider;
use DateTime;
use DateTimeInterface;
use Ramsey\Uuid\Uuid;

class UserNotificationChannelRequestManager
{
    public function __construct(
        private readonly UserNotificationChannelManager $userNotificationChannelManager,
        private readonly UserNotificationChannelProvider $userNotificationChannelProvider,
        private readonly UserProvider $userProvider,
    ) {
    }

    public function createOrUpdateFromRequest(
        UserNotificationChannelRequest $userNotificationChannelRequest
    ): UserNotificationChannel {
        $userId = Uuid::fromString($userNotificationChannelRequest->userId);

        $userNotificationChannel = $this->userNotificationChannelProvider->findOneByUserAndChannel(
            $userId,
            $userNotificationChannelRequest->channel
        );

        if (null === $userNotificationChannel) {
            $userNotificationChannel = new UserNotificationChannel();
        }

        $this->mapFromRequest($userNotificationChannel, $userNotificationChannelRequest);

        $this->userNotificationChannelManager->save($userNotificationChannel);

        return $userNotificationChannel;
    }


    public function mapFromRequest(
        UserNotificationChannel $userNotificationChannel,
        UserNotificationChannelRequest $userNotificationChannelRequest,
    ): void {
        if ($userNotificationChannelRequest->has('userId')) {
            $userNotificationChannel->setUser(
                $this->userProvider->get(Uuid::fromString($userNotificationChannelRequest->userId))
            );
        }

        $userNotificationChannel->setChannel($userNotificationChannelRequest->channel);

        if ($userNotificationChannelRequest->has('externalId')) {
            $userNotificationChannel->setExternalId($userNotificationChannelRequest->externalId);
        }

        if ($userNotificationChannelRequest->has('details')) {
            $userNotificationChannel->setDetails($userNotificationChannelRequest->details);
        }

        if ($userNotificationChannelRequest->has('expiresAt')) {
            $userNotificationChannel->setExpiresAt(
                DateTime::createFromFormat(DateTimeInterface::ATOM, $userNotificationChannelRequest->expiresAt)
            );
        }
    }
}