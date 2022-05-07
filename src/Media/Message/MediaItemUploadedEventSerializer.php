<?php

namespace App\Media\Message;

use App\Core\Messenger\ExternalJsonMessageSerializerInterface;
use App\Media\Provider\MediaItemProvider;
use Exception;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;

class MediaItemUploadedEventSerializer implements ExternalJsonMessageSerializerInterface
{
    public const EVENT_SOURCE = 'aws:s3';
    public const EVENT_NAME = 'ObjectCreated:Put';

    public function __construct(
        private readonly MediaItemProvider $mediaItemProvider,
    ) {
    }

    public function supports(string $eventSource, string $eventName): bool
    {
        return $eventSource === self::EVENT_SOURCE && $eventName === self::EVENT_NAME;
    }

    public function parse(array $record): MediaItemUploadedEvent
    {
        try {
            $mediaItemId = Uuid::fromString(pathinfo($record['s3']['object']['key'],PATHINFO_FILENAME));

            $this->mediaItemProvider->get($mediaItemId);

            return new MediaItemUploadedEvent($mediaItemId);
        } catch (Exception) {
            throw new MessageDecodingFailedException('Invalid media item ID');
        }
    }
}