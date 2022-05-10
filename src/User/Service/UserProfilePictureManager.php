<?php

declare(strict_types=1);

namespace App\User\Service;

use App\User\Entity\User;
use App\User\Exception\UserInvalidProfilePictureException;
use Intervention\Image\ImageManagerStatic as Image;
use League\Flysystem\FilesystemOperator;
use Ramsey\Uuid\Uuid;

class UserProfilePictureManager
{
    public const MAX_WIDTH = 1024;
    public const MAX_HEIGHT = 1024;
    public const FILE_PATH = 'profile/';

    public function __construct(
        private readonly UserManager $userManager,
        private readonly FilesystemOperator $userImageFilesystem,
    ) {
    }

    public function uploadImageContents(User $user, string $photoContents, ?string $mimeType = null): void
    {
        try {
            $tempImageFile = tempnam("/tmp", "user-photo-");
            file_put_contents($tempImageFile, $photoContents);
            $image = Image::make($tempImageFile);
            $image->orientate();
            $image->resize(self::MAX_WIDTH, self::MAX_HEIGHT, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            $filename = self::FILE_PATH.Uuid::uuid4().'.png';

            $config = [
                'ACL' => 'public-read',
            ];

            if (null !== $mimeType) {
                $config['Content-Type'] = $mimeType;
            }

            $this->userImageFilesystem->write(
                $filename,
                $image->encode('png')->getEncoded(),
                $config,
            );

            $user->setProfilePicture($filename);
            $this->userManager->update($user);

            unlink($tempImageFile);
        } catch (\Exception) {
            throw new UserInvalidProfilePictureException();
        }
    }
}
