<?php

namespace App\Core\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\Translation\TranslatorInterface;

class EmailComposer
{
    public function __construct(
        private TranslatorInterface $translator,
        private string $unsubscribeUrl,
    ) {
    }

    public function applyTemplate(TemplatedEmail $email, string $identifier, array $context, ?string $locale = null)
    {
        $currentLocale = $this->translator->getLocale();

        if (null === $locale) {
            $locale = $this->translator->getFallbackLocales()[0];
        }

        $templateFile = str_replace('.', '/', $identifier);

        $subjectTranslationKey = $identifier.'.subject';
        $subject = $this->translator->trans($subjectTranslationKey, [], 'email', $locale);

        $email
            ->subject($subject)
            ->htmlTemplate('email/'.$templateFile.'.html.twig')
            ->context(
                array_merge(
                    [
                        'recipient_email' => $email->getTo()[0]->getAddress(),
                        'identifier' => $identifier,
                        'subject' => $subject,
                        'unsubscribe_url' => $this->unsubscribeUrl,
                    ],
                    $context
                )
            );

        $this->translator->setLocale($currentLocale);
    }

    public function compose(string $identifier, array $recipients, array $context = [], ?string $locale = null): Email
    {
        $email = new TemplatedEmail();

        foreach ($recipients as $recipientName => $recipientEmail) {
            $email->addTo(new Address($recipientEmail, $recipientName));
        }

        $this->applyTemplate($email, $identifier, $context, $locale);

        return $email;
    }
}
