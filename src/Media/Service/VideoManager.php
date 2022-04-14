<?php

namespace App\Media\Service;

use App\Core\Validation\EntityValidator;
use App\Media\Entity\Video;
use App\Media\Repository\VideoRepository;

class VideoManager
{
    public function __construct(
        private VideoRepository $videoRepository,
        private EntityValidator $validator,
    ) {
    }

    public function create(Video $video): void
    {
        $this->save($video);
    }

    public function update(Video $video): void
    {
        $this->save($video);
    }

    public function delete(object $video): void
    {
        $this->videoRepository->delete($video);
    }

    public function save(Video $video): void
    {
        $this->validator->validate($video);

        $this->videoRepository->save($video);
    }
}
