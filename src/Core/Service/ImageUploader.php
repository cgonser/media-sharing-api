<?php

namespace App\Core\Service;

use App\Core\Exception\InvalidImageException;
use Aws\S3\S3Client;
use League\Flysystem\Filesystem;
use Ramsey\Uuid\Uuid;

class ImageUploader
{
    public function __construct(
        private Filesystem $fileSystem,
        private S3Client $s3Client,
        private string $s3BucketName,
    ) {
    }

    public function uploadImage(string $imageEncodedContents, bool $public = true): ?string
    {
        $imageContents = $this->decodeImageContents($imageEncodedContents);

        if (null === $imageContents) {
            return null;
        }

        return $this->uploadRawImage($imageContents, $public);
    }

    public function uploadRawImage(string $imageContents, bool $public = true, ?string $imageType = null): ?string
    {
        if ($imageType === 'svg') {
            $extension = 'svg';
        } else {
            $imageInfo = getimagesizefromstring($imageContents);

            if (false === $imageInfo) {
                throw new InvalidImageException();
            }

            $extension = explode('/', $imageInfo['mime'])[1];
        }

        $filename = Uuid::uuid4().'.'.$extension;

        $config = [];

        if ($public) {
            $config['ACL'] = 'public-read';
        }

        $this->fileSystem->write($filename, $imageContents, $config);

        return $filename;
    }

    public function decodeImageContents(string $encodedContents): ?string
    {
        return null !== $encodedContents
            ? base64_decode($encodedContents)
            : null;
    }

    public function getImagePublicUrl(string $filename): string
    {
        return $this->s3Client->getObjectUrl(
            $this->s3BucketName,
            $filename
        );
    }
}
