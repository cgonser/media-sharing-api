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
        private readonly VideoManager $videoManager,
        private readonly MomentProvider $momentProvider,
        private readonly UserProvider $userProvider,
        private readonly LocationRequestManager $locationRequestManager,
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

        if ($videoRequest->has('moods')) {
            $video->setMoods($videoRequest->moods);
        }

        if ($videoRequest->has('locations')) {
            $this->mapVideoLocations($video, $videoRequest->locations);
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

    private function mapVideoLocations(Video $video, ?array $locationRequests): void
    {
        $locations = [];
        foreach ($locationRequests as $locationRequest) {
            $locations[] = $this->locationRequestManager->createFromRequest($locationRequest);
        }

        $video->setLocations($locations);
    }

    private function mapVideoMoments(Video $video, ?array $videoMomentRequests): void
    {
        foreach ($videoMomentRequests as $videoMomentRequest) {
            $moment = $this->momentProvider->getByUserAndId(
                $video->getUserId(),
                Uuid::fromString($videoMomentRequest->momentId)
            );

            if ($video->hasMoment($moment)) {
                $video->updateMoment($moment, $videoMomentRequest->position, $videoMomentRequest->duration);
            } else {
                $video->addMoment($moment, $videoMomentRequest->position, $videoMomentRequest->duration);
            }
        }
    }
}
