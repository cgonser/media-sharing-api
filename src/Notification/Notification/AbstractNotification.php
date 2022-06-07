<?php

namespace App\Notification\Notification;

use App\Notification\Entity\UserNotificationChannel;
use App\Notification\Exception\NotificationSettingsNotFoundException;
use Exception;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Notifier\Bridge\Firebase\Notification\AndroidNotification;
use Symfony\Component\Notifier\Bridge\Firebase\Notification\IOSNotification;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Notification\ChatNotificationInterface;
use Symfony\Component\Notifier\Notification\EmailNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;
use Symfony\Component\Notifier\Recipient\RecipientInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractNotification extends Notification implements EmailNotificationInterface, ChatNotificationInterface
{
    public const TYPE = null;

    protected TranslatorInterface $translator;

    protected array $context = [];

    protected string $locale;

    public function __construct(?array $context = [])
    {
        if (null === $this::TYPE) {
            throw new Exception("A notification should define the TYPE constant");
        }

        if (null !== $context) {
            $this->context = $context;
        }

        parent::__construct();
    }

    public function setTranslator(TranslatorInterface $translator): self
    {
        $this->translator = $translator;

        return $this;
    }

    public function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function setContext(array $context): self
    {
        $this->context = $context;

        return $this;
    }

    public function asChatMessage(RecipientInterface $recipient, string $transport = null): ?ChatMessage
    {
        $chatMessage = ChatMessage::fromNotification($this);
        /** @var UserNotificationChannel $userNotificationChannel */
        $userNotificationChannel = $recipient->getUserNotificationChannel();

        if (null === $userNotificationChannel) {
            return null;
        }

        $messageOptions = null;

        if ('ios' === $userNotificationChannel->getDeviceType()) {
            $messageOptions = new IOSNotification(
                $userNotificationChannel->getToken(),
                [
                    'to' => $userNotificationChannel->getToken(),
                ]
            );
        }

        if ('android' === $userNotificationChannel->getDeviceType()) {
            $messageOptions = new AndroidNotification(
                $userNotificationChannel->getToken(),
                [
                    'to' => $userNotificationChannel->getToken(),
                ]
            );
        }

        if (null === $messageOptions) {
            throw new NotificationSettingsNotFoundException();
        }

        $chatMessage->options($messageOptions);

        return $chatMessage;
    }

    public function asEmailMessage(EmailRecipientInterface $recipient, string $transport = null): ?EmailMessage
    {
        $email = new TemplatedEmail();
        $email->addTo($recipient->getEmail());

        $this->applyEmailTemplate($email, $this::TYPE);

        return new EmailMessage($email);
    }

    public function applyEmailTemplate(TemplatedEmail $email, string $identifier): void
    {
        $templateFile = str_replace('.', '/', $identifier);

        $subjectTranslationKey = $identifier.'.subject';
        $subject = $this->translate($subjectTranslationKey, 'email');

        $email
            ->subject($subject)
            ->htmlTemplate('email/'.$templateFile.'.html.twig')
            ->context(
                array_merge(
                    [
                        'recipient_email' => $email->getTo()[0]->getAddress(),
                        'identifier' => $identifier,
                        'subject' => $subject,
                    ],
                    $this->context,
                )
            );
    }

    public function translate(string $translationKey, string $domain): string
    {
        return $this->translator->trans(
            $translationKey,
            $this->convertContextToPlaceholders($this->context),
            $domain,
            $this->locale
        );
    }

    private function convertContextToPlaceholders(array $context): array
    {
        $placeholders = [];

        foreach ($context as $key => $value) {
            $placeholders['%'.$key.'%'] = $value;
        }

        return $placeholders;
    }

    abstract public function getAvailableChannels(): array;
}