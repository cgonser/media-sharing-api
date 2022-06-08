<?php

namespace App\Core\Messenger;

use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

class ExternalJsonMessageSerializer implements SerializerInterface
{
    public function __construct(
        private readonly iterable $serializers,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function decode(array $encodedEnvelope): Envelope
    {
        $body = $encodedEnvelope['body'];
        $data = json_decode($body,true);

        if (!isset($data['Records'])) {
            throw new MessageDecodingFailedException("Record entries not found. Could not decode message: ".$encodedEnvelope['body']);
        }

        $message = new MessageBatch();

        foreach ($data['Records'] as $record) {
            foreach ($this->serializers as $serializer) {
                try {
                    if ($serializer->supports($record['eventSource'], $record['eventName'])) {
                        $message->add($serializer->parse($record));
                    }
                } catch (Exception $e) {
                    $this->logger->error($e->getMessage());
                }
            }
        }

        return new Envelope($message);
    }

    public function encode(Envelope $envelope): array
    {
        throw new \Exception('Transport & serializer not meant for sending messages');
    }
}
