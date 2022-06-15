<?php

namespace App\Media\Message;

use App\Core\Messenger\ExternalJsonMessageSerializerInterface;
use App\Media\Provider\MediaItemProvider;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;

class MediaItemUploadedEventSerializer implements ExternalJsonMessageSerializerInterface
{
    public const EVENT_SOURCE = 'aws:s3';
    public const EVENT_NAME = 'ObjectCreated:Put';

    public function __construct(
        private readonly MediaItemProvider $mediaItemProvider,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function supports(string $eventSource, string $eventName): bool
    {
        return $eventSource === self::EVENT_SOURCE && $eventName === self::EVENT_NAME;
    }

    public function parse(array $record): MediaItemUploadedEvent
    {
        try {
            $objectKey = $record['s3']['object']['key'];

            $this->logger->info('s3.ObjectCreated', [
                'objectKey' => $objectKey,
            ]);

            $mediaItem = $this->mediaItemProvider->getByFilename($objectKey);

            return new MediaItemUploadedEvent(
                mediaItemId: $mediaItem->getId(),
                filename: $objectKey
            );
        } catch (Exception) {
            throw new MessageDecodingFailedException('Media item not found: '.$objectKey);
        }
    }
}
