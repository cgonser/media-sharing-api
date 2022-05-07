<?php

namespace App\Core\Messenger;

interface ExternalJsonMessageSerializerInterface
{
    public function supports(string $eventSource, string $eventName): bool;
}