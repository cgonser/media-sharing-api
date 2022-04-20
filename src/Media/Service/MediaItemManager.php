<?php

namespace App\Media\Service;

use App\Core\Validation\EntityValidator;
use App\Media\Entity\MediaItem;
use App\Media\Repository\MediaItemRepository;
use Aws\S3\S3Client;

class MediaItemManager
{
    private const S3_UPLOAD_EXPIRES_AFTER = '+24 hour';

    public function __construct(
        private MediaItemRepository $mediaItemRepository,
        private EntityValidator $validator,
        private S3Client $s3Client,
        private string $s3BucketName,
    ) {
    }

    public function createUploadableItem(string $type, string $extension): MediaItem
    {
        $mediaItem = new MediaItem();
        $mediaItem->setStatus(MediaItem::STATUS_UPLOAD_PENDING);
        $mediaItem->setType($type);
        $mediaItem->setExtension($extension);
        $this->save($mediaItem);

        $mediaItem->setFilename($mediaItem->getId()->toString().'.'.$extension);
        $mediaItem->setUploadUrlValidUntil(new \DateTime(self::S3_UPLOAD_EXPIRES_AFTER));

        $uploadRequest = $this->s3Client->createPresignedRequest(
            $this->s3Client->getCommand('PutObject', [
                'Bucket' => $this->s3BucketName,
                'Key' => $mediaItem->getFilename(),
            ]),
            self::S3_UPLOAD_EXPIRES_AFTER
        );

        $mediaItem->setUploadUrl((string)$uploadRequest->getUri());

        $this->save($mediaItem);

        return $mediaItem;
    }

    public function updateUploadStatus(MediaItem $mediaItem): void
    {
        try {
            $this->s3Client->getObject([
                'Bucket' => $this->s3BucketName,
                'Key' => $mediaItem->getFilename(),
            ]);

            $mediaItem->setPublicUrl($this->s3Client->getObjectUrl($this->s3BucketName, $mediaItem->getFilename()));
            $mediaItem->setStatus(MediaItem::STATUS_AVAILABLE);

            $this->save($mediaItem);
        } catch (\Exception $e) {
            if (new \DateTime() > $mediaItem->getUploadUrlValidUntil()) {
                $this->delete($mediaItem);
            }
        }
    }

    public function create(MediaItem $mediaItem): void
    {
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
