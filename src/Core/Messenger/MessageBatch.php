<?php

namespace App\Core\Messenger;

class MessageBatch
{
    private array $messages = [];

    public function add(object $message): void
    {
        $this->messages[] = $message;
    }

    public function getMessages(): array
    {
        return $this->messages;
    }
}