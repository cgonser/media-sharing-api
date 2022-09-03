<?php

declare(strict_types=1);

namespace App\User\Service;

use App\User\Entity\User;
use App\User\Exception\UserInvalidProfilePictureException;
use Exception;
use Intervention\Image\Gd\Font;
use Intervention\Image\ImageManagerStatic as Image;
use League\Flysystem\FilesystemOperator;
use Ramsey\Uuid\Uuid;

class UserImageManager
{
    public const MAX_WIDTH = 1024;
    public const MAX_HEIGHT = 1024;
    public const PROFILE_IMAGE_PATH = 'profile/';
    public const HANDLE_IMAGE_PATH = 'handle/';
    public const HANDLE_BASE = 'handle_base.png';

    public function __construct(
        private readonly UserManager $userManager,
        private readonly FilesystemOperator $userImageFilesystem,
        private readonly string $fontPath,
        private readonly string $imagePath,
        private readonly string $s3BucketName,
    ) {
    }

    public function uploadProfilePicture(User $user, string $photoContents, ?string $mimeType = null): void
    {
        try {
            $tempImageFile = tempnam('/tmp', 'user-photo-');
            file_put_contents($tempImageFile, $photoContents);
            $image = Image::make($tempImageFile);
            $image->orientate();
            $image->resize(self::MAX_WIDTH, self::MAX_HEIGHT, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            $filename = self::PROFILE_IMAGE_PATH.Uuid::uuid4().'.jpg';

            $config = [
                'ACL' => 'public-read',
                'Content-Type' => 'image/jpg',
            ];

            $this->userImageFilesystem->write(
                $filename,
                $image->encode('jpg')->getEncoded(),
                $config,
            );

            $user->setProfilePicture($filename);
            $this->userManager->update($user);

            unlink($tempImageFile);
        } catch (Exception) {
            throw new UserInvalidProfilePictureException();
        }
    }

    public function getOrCreateHandleImage(User $user): string
    {
        return $this->s3BucketName.'/'.$this->persistHandleImage($user, $this->generateHandleImage($user));
    }

    public function getOrCreateHandleWithLogoImage(User $user): string
    {
        return $this->s3BucketName.'/'.$this->persistHandleImage($user, $this->generateHandleWithLogoImage($user), '-logo');
    }

    public function persistHandleImage(User $user, string $imageContents, ?string $suffix = ''): string
    {
        $filename = self::HANDLE_IMAGE_PATH.$user->getId()->toString().$suffix.'.png';

        $this->userImageFilesystem->write(
            $filename,
            $imageContents,
            [
                'ACL' => 'public-read',
                'Content-Type' => 'image/png',
            ],
        );

        return $filename;
    }

    public function generateHandleImage(
        User $user,
        ?int $width = 720,
        ?int $fontSize = 42,
        array|string|null $fontColor = '#ffffff',
    ): string {
        $img = Image::canvas($width, $fontSize + 8);

        if (null === $fontColor || 'transparent' === $fontColor) {
            $fontColor = [255, 255, 255, 0.5];
        }

        $img->text($user->getUsername(), $width / 2, $fontSize, function (Font $font) use ($fontColor, $fontSize) {
            $font->size($fontSize);
            $font->file($this->fontPath.'Inter-Bold.ttf');
            $font->align('center');
            $font->color($fontColor);
        });

        $img->encode('png');
        $img->save('/app/var/handle.png');

        return $img->getEncoded();
    }

    public function generateHandleWithLogoImage(
        User $user,
        ?int $fontSize = 36,
        array|string|null $fontColor = '#ffffff',
    ): string {
        $img = Image::make(file_get_contents($this->imagePath.self::HANDLE_BASE));

        if (null === $fontColor || 'transparent' === $fontColor) {
            $fontColor = [255, 255, 255, 0.5];
        }

        $img->text($user->getUsername(), 106, 44, function (Font $font) use ($fontColor, $fontSize) {
            $font->size($fontSize);
            $font->file($this->fontPath.'Inter-Bold.ttf');
            $font->color($fontColor);
        });

        $img->encode('png');
        $img->save('/app/var/handle-logo.png');
//        exit;

        return $img->getEncoded();
    }
}
