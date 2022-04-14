<?php

declare(strict_types=1);

namespace App\Core\EventSubscriber;

use App\Core\Exception\ApiJsonInputValidationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ConstraintViolationEventSubscriber implements EventSubscriberInterface
{
    public function onControllerArguments(ControllerArgumentsEvent $event): void
    {
        foreach ($event->getArguments() as $argument) {
            if ($argument instanceOf ConstraintViolationListInterface) {
                if ($argument->count() === 0) {
                    return;
                }

                throw new ApiJsonInputValidationException($argument);
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER_ARGUMENTS => 'onControllerArguments',
        ];
    }
}
