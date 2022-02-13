<?php

declare(strict_types=1);

namespace App\Core\EventSubscriber;

use App\Core\Exception\ApiJsonInputValidationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\ConstraintViolationList;

class ConstraintViolationEventSubscriber implements EventSubscriberInterface
{
    public function onControllerArguments($event): void
    {
        if (!$event->getRequest()->attributes->has('constraintViolationList')) {
            return;
        }

        $constraintViolationList = $event->getRequest()->attributes->get('constraintViolationList');
        if ($constraintViolationList->count() === 0) {
            return;
        }

        throw new ApiJsonInputValidationException($constraintViolationList);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER_ARGUMENTS => 'onControllerArguments',
        ];
    }
}
