<?php

namespace App\Core\Messenger;

use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class MessageBatchHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    public function __invoke(MessageBatch $messageBatch)
    {
        foreach ($messageBatch->getMessages() as $message) {
            $this->messageBus->dispatch($message);
        }
    }
}
