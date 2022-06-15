<?php

namespace App\Core\Messenger;

use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
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
        $data = json_decode($body, true);

        $this->logger->warning('message', $encodedEnvelope);

        try {
            if (isset($data['source'], $data['detail-type'])) {
                return $this->processSimpleEvent($data);
            }

            if (isset($data['Records'])) {
                return $this->processEventWithRecords($data);
            }
        } catch (Exception $e) {
            $this->logger->warning(
                'Could not decode message',
                [
                    'error' => $e->getMessage(),
                    'message' => $encodedEnvelope,
                ]
            );
        }

        return new Envelope(new GenericEvent());
    }

    private function processSimpleEvent(array $eventData): ?Envelope
    {
        foreach ($this->serializers as $serializer) {
            if ($serializer->supports($eventData['source'], $eventData['detail-type'])) {
                $event = $serializer->parse($eventData);

                return $event !== null ? new Envelope($event) : null;
            }
        }

        throw new MessageDecodingFailedException(
            "Could not find a serializer",
            [
                'source' => $eventData['source'],
                'detail-type' => $eventData['detail-type'],
            ]
        );
    }

    private function processEventWithRecords(array $eventData): Envelope
    {
        $message = new MessageBatch();

        foreach ($eventData['Records'] as $record) {
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
