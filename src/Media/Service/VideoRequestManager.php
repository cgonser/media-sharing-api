<?php

namespace App\Media\Service;

use App\Media\Entity\Video;
use App\Media\Provider\MomentProvider;
use App\Media\Request\VideoRequest;
use App\User\Provider\UserProvider;
use Ramsey\Uuid\Uuid;

class VideoRequestManager
{
    public function __construct(
        private VideoManager $videoManager,
        private MomentProvider $momentProvider,
        private UserProvider $userProvider,
    ) {
    }

    public function createFromRequest(VideoRequest $videoRequest): Video
    {
        $video = new Video();

        $this->mapFromRequest($video, $videoRequest);

        $this->videoManager->create($video);

        return $video;
    }

    public function updateFromRequest(Video $video, VideoRequest $videoRequest): void
    {
        $this->mapFromRequest($video, $videoRequest);

        $this->videoManager->update($video);
    }

    public function mapFromRequest(Video $video, VideoRequest $videoRequest): void
    {
        if ($videoRequest->has('userId')) {
            $video->setUser(
                $this->userProvider->get(Uuid::fromString($videoRequest->userId))
            );
        }

        if ($videoRequest->has('description')) {
            $video->setDescription($videoRequest->description);
        }

        if ($videoRequest->has('mood')) {
            $video->setMood($videoRequest->mood);
        }

        if ($videoRequest->has('thumbnail')) {
            $video->setThumbnail($videoRequest->thumbnail);
        }

        if ($videoRequest->has('locations')) {
            $video->setLocations($videoRequest->locations);
        }

        if ($videoRequest->has('moments')) {
            $this->mapVideoMoments($video, $videoRequest->moments);
        }

        if ($videoRequest->has('duration')) {
            $video->setDuration($videoRequest->duration);
        }

        if ($videoRequest->has('recordedAt')) {
            $video->setRecordedAt(
                \DateTime::createFromFormat(\DateTimeInterface::ATOM, $videoRequest->recordedAt)
            );
        }
    }

    private function mapVideoMoments(Video $video, ?array $videoMomentRequests): void
    {
        foreach ($videoMomentRequests as $videoMomentRequest) {
            $moment = $this->momentProvider->getByUserAndId(
                $video->getUserId(),
                Uuid::fromString($videoMomentRequest->momentId)
            );

            if ($video->hasMoment($moment)) {
                $video->updateMoment($moment, $videoMomentRequest->position);
            } else {
                $video->addMoment($moment, $videoMomentRequest->position);
            }
        }
    }
}
