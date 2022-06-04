<?php

namespace App\Notification\Recipient;

use App\Notification\Entity\UserNotificationChannel;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;
use Symfony\Component\Notifier\Recipient\EmailRecipientTrait;
use Symfony\Component\Notifier\Recipient\SmsRecipientInterface;
use Symfony\Component\Notifier\Recipient\SmsRecipientTrait;

class Recipient implements EmailRecipientInterface, SmsRecipientInterface
{
    use EmailRecipientTrait;
    use SmsRecipientTrait;

    private ?UserNotificationChannel $userNotificationChannel = null;

    public function email(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function phone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function userNotificationChannel(?UserNotificationChannel $userNotificationChannel): self
    {
        $this->userNotificationChannel = $userNotificationChannel;

        return $this;
    }

    public function getUserNotificationChannel(): ?UserNotificationChannel
    {
        return $this->userNotificationChannel;
    }
}