<?php

namespace App\Notification\Notification;

use Exception;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Notification\EmailNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractNotification extends Notification  implements EmailNotificationInterface
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

    public function setTranslator(TranslatorInterface $translator): void
    {
        $this->translator = $translator;
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function setContext(array $context): void
    {
        $this->context = $context;
    }

    public function asEmailMessage(EmailRecipientInterface $recipient, string $transport = null): ?EmailMessage
    {
        $email = new TemplatedEmail();
        $email->addTo($recipient->getEmail());

        $this->applyTemplate($email, $this::TYPE, $this->context, $this->locale);

        return new EmailMessage($email);
    }

    public function applyTemplate(TemplatedEmail $email, string $identifier, array $context, ?string $locale = null)
    {
        $templateFile = str_replace('.', '/', $identifier);

        $subjectTranslationKey = $identifier.'.subject';
        $subject = $this->translator->trans(
            $subjectTranslationKey,
            $this->convertContextToPlaceholders($context),
            'email',
            $locale
        );

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
                    $context,
                )
            );
    }

    abstract public function getAvailableChannels(): array;

    private function convertContextToPlaceholders(array $context): array
    {
        $placeholders = [];

        foreach ($context as $key => $value) {
            $placeholders['%'.$key.'%'] = $value;
        }

        return $placeholders;
    }
}