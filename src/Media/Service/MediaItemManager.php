<?php

namespace App\Media\Service;

use App\Core\Validation\EntityValidator;
use App\Media\Entity\MediaItem;
use App\Media\Enumeration\MediaItemExtension;
use App\Media\Enumeration\MediaItemStatus;
use App\Media\Enumeration\MediaItemType;
use App\Media\Repository\MediaItemRepository;
use Aws\S3\S3Client;
use Exception;
use Psr\Log\LoggerInterface;

class MediaItemManager
{
    private const S3_UPLOAD_EXPIRES_AFTER = '+24 hour';

    public function __construct(
        private readonly MediaItemRepository $mediaItemRepository,
        private readonly EntityValidator $validator,
        private readonly S3Client $s3Client,
        private readonly string $s3BucketName,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function uploadFile(string $key, mixed $contents, ?string $contentType = null): ?string
    {
        $config = [
            'Bucket' => $this->s3BucketName,
            'Key' => $key,
            'Body' => $contents,
            'ACL' => 'public-read',
        ];

        if (null !== $contentType) {
            $config['Content-Type'] = $contentType;
        }

        $output = $this->s3Client->putObject($config);

        return $output->get('ObjectURL') ?? $this->s3Client->getObjectUrl($this->s3BucketName, $key);
    }

    public function createUploadableItem(MediaItemType $type, MediaItemExtension $extension): MediaItem
    {
        $mediaItem = new MediaItem();
        $mediaItem->setStatus(MediaItemStatus::UPLOAD_PENDING);
        $mediaItem->setType($type);
        $mediaItem->setExtension($extension);
        $this->save($mediaItem);

        $mediaItem->setFilename($mediaItem->getId()->toString().'.'.$extension->value);

//        $mediaItem->setUploadUrlValidUntil(new \DateTime(self::S3_UPLOAD_EXPIRES_AFTER));
//        $uploadRequest = $this->s3Client->createPresignedRequest(
//            $this->s3Client->getCommand('PutObject', [
//                'Bucket' => $this->s3BucketName,
//                'Key' => $mediaItem->getFilename(),
//            ]),
//            self::S3_UPLOAD_EXPIRES_AFTER
//        );
//        $mediaItem->setUploadUrl((string)$uploadRequest->getUri());

        $this->save($mediaItem);

        return $mediaItem;
    }

    public function refreshStatus(MediaItem $mediaItem, bool $forceUpdate = false): void
    {
        if (!$forceUpdate && MediaItemStatus::AVAILABLE === $mediaItem->getStatus()) {
            return;
        }

        try {
            $publicUrl = $this->s3Client->getObjectUrl($this->s3BucketName, $mediaItem->getFilename());

            if (null === $publicUrl) {
                return;
            }

            $mediaItem->setPublicUrl($publicUrl);
            $mediaItem->setStatus(MediaItemStatus::AVAILABLE);

            $this->save($mediaItem);
        } catch (Exception $e) {
            $this->logger->warning(
                'media_item.refresh_status',
                [
                    'id' => $mediaItem->getId()->toString(),
                    'error' => $e->getMessage(),
                ]
            );
        }
    }

    public function create(MediaItem $mediaItem): void
    {
        if (null === $mediaItem->getStatus()) {
            $mediaItem->setStatus(MediaItemStatus::UPLOAD_PENDING);
        }

        $this->save($mediaItem);
    }

    public function update(MediaItem $mediaItem): void
    {
        $this->save($mediaItem);
    }

    public function delete(object $mediaItem): void
    {
        $this->mediaItemRepository->delete($mediaItem);
    }

    public function save(MediaItem $mediaItem): void
    {
        $this->validator->validate($mediaItem);

        $this->mediaItemRepository->save($mediaItem);
    }
}
