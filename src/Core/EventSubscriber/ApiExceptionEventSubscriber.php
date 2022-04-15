<?php

namespace App\Core\EventSubscriber;

use App\Core\Exception\ApiJsonException;
use App\Core\Response\ApiJsonErrorResponse;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

class ApiExceptionEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $event->setResponse(
            $this->prepareResponse($event->getThrowable())
        );
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    private function prepareResponse(\Throwable $e)
    {
        if ($e instanceof HandlerFailedException) {
            return $this->prepareResponse($e->getNestedExceptions()[0]);
        }

        if ($e instanceof ApiJsonException) {
            return new ApiJsonErrorResponse(
                $e->getStatusCode(),
                $e->getMessage(),
                $e->getErrors()
            );
        }

        if ($e instanceof HttpException) {
            return new ApiJsonErrorResponse($e->getStatusCode(), $e->getMessage());
        }

        if ($e instanceof InvalidArgumentException) {
            return new ApiJsonErrorResponse(400, $e->getMessage());
        }

        $this->logger->error(
            'error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTrace(),
            ]
        );

        $errorCode = $e->getCode() && $e->getCode() >= 100 && $e->getCode() <= 600
            ? $e->getCode()
            : Response::HTTP_INTERNAL_SERVER_ERROR;

        return new ApiJsonErrorResponse($errorCode, $e->getMessage());
    }
}
