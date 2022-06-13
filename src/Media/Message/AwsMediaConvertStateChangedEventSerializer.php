<?php

namespace App\Media\Message;

use App\Core\Messenger\ExternalJsonMessageSerializerInterface;
use Psr\Log\LoggerInterface;

class AwsMediaConvertStateChangedEventSerializer implements ExternalJsonMessageSerializerInterface
{
    public const EVENT_SOURCE = 'aws.mediaconvert';
    public const EVENT_NAME = 'MediaConvert Job State Change';

    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    public function supports(string $eventSource, string $eventName): bool
    {
        return $eventSource === self::EVENT_SOURCE && $eventName === self::EVENT_NAME;
    }

    public function parse(array $record): MediaItemUploadedEvent
    {
        $jobId = $record['detail']['jobId'];
        $status = $record['detail']['status'];

        $this->logger->info('mediaconvert.status_change', [
            'jobId' => $jobId,
            'status' => $status,
        ]);

        return new MediaItemUploadedEvent(awsJobId: $jobId);
    }
}
