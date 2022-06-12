<?php

namespace App\Media\ResponseMapper;

use App\Media\Dto\MomentDateDto;
use App\Media\Dto\MomentDto;
use App\Media\Dto\MomentMoodClusterDto;
use App\Media\Dto\MomentMoodDto;
use App\Media\Dto\MomentMoodMapDto;
use App\Media\Entity\MediaItem;
use App\Media\Entity\Moment;
use App\Media\Entity\MomentMediaItem;
use App\Media\Provider\MomentProvider;
use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;

class MomentResponseMapper
{
    public function __construct(
        private readonly MomentProvider $momentProvider,
        private readonly LocationResponseMapper $locationResponseMapper,
    ) {
    }

    public function map(Moment $moment): MomentDto
    {
        $momentDto = new MomentDto();
        $momentDto->id = $moment->getId()->toString();
        $momentDto->userId = $moment->getUser()->getId()->toString();
        $momentDto->status = $moment->getStatus()->value;
        $momentDto->mood = $moment->getMood()->value;
        $momentDto->duration = $moment->getDuration();
        $momentDto->recordedOn = $moment->getRecordedAt()?->format('Y-m-d');
        $momentDto->recordedAt = $moment->getRecordedAt()?->format(DateTimeInterface::ATOM);
        $momentDto->mediaItems = !$moment->getMomentMediaItems()->isEmpty()
            ? $this->mapMediaItems($moment->getMomentMediaItems()->toArray())
            : null;

        if (null !== $moment->getLocation()) {
            $momentDto->location = $this->locationResponseMapper->map($moment->getLocation());
        }

        return $momentDto;
    }

    public function mapMultiple(array $moments): array
    {
        return array_map(
            fn ($moment) => $this->map($moment),
            $moments
        );
    }

    private function mapMediaItems(array $momentMediaItems): array
    {
        $return = [];

        /** @var MomentMediaItem $momentMediaItems */
        foreach ($momentMediaItems as $momentMediaItem) {
            /** @var MediaItem $mediaItem */
            $mediaItem = $momentMediaItem->getMediaItem();
            $return[$mediaItem->getType()->value] = $mediaItem->getPublicUrl();
        }

        return $return;
    }

    public function mapGroupedBy(array $moments, ?string $groupBy): array
    {
        $groupedResults = [];

        $momentDtos = $this->mapMultiple($moments);

        /** @var MomentDto $momentDto */
        foreach ($momentDtos as $momentDto) {
            $groupByValue = $momentDto->{$groupBy};

            if (!isset($groupedResults[$groupByValue])) {
                $groupedResults[$groupByValue] = [];
            }

            $groupedResults[$groupByValue][] = $momentDto;
        }

        return $groupedResults;
    }

    public function mapRecordedOnDates(array $results, ?bool $expandMoments = false, ?UuidInterface $userId = null): array
    {
        $return = [];

        foreach ($results as $result) {
            $momentDateDto = new MomentDateDto();
            $momentDateDto->recordedOn = $result['recordedOn']->format('Y-m-d');
            $momentDateDto->count = $result['count'];

            if ($expandMoments && null !== $userId) {
                $momentDateDto->moments = $this->mapMultiple(
                    $this->momentProvider->findByRecordedOn($userId, $result['recordedOn'])
                );
            }

            $return[] = $momentDateDto;
        }

        return $return;
    }

    public function mapMoodMap(array $results): MomentMoodMapDto
    {
        $momentMoodMapDto = new MomentMoodMapDto();
        $clusters = [];

        foreach ($results as $result) {
            if (null === $result['cluster_id']) {
                $momentMoodMapDto->moments[] = $this->mapMomentMood($result);

                continue;
            }

            $clusterId = $result['mood'] . '_' . $result['cluster_id'];

            if (!isset($clusters[$clusterId])) {
                $clusters[$clusterId] = [
                    'mood' => $result['mood'],
                    'moments' => [],
                ];
            }

            $clusters[$clusterId]['moments'][] = $this->mapMomentMood($result);
        }

        ksort($clusters);

        foreach ($clusters as $cluster) {
            $momentMoodMapDto->clusters[] = $this->mapMomentMoodCluster($cluster['mood'], $cluster['moments']);
        }

        return $momentMoodMapDto;
    }

    private function mapMomentMoodCluster(string $mood, array $moments): MomentMoodClusterDto
    {
        $momentMoodClusterDto = new MomentMoodClusterDto();
        $momentMoodClusterDto->mood = $mood;
        $momentMoodClusterDto->moments = $moments;

        return $momentMoodClusterDto;
    }

    private function mapMomentMood(array $result): MomentMoodDto
    {
        $momentMoodDto = new MomentMoodDto();
        $momentMoodDto->mood = $result['mood'];
        $momentMoodDto->long = $result['long'];
        $momentMoodDto->lat = $result['lat'];
        $momentMoodDto->moments = $result['moments'];

        return $momentMoodDto;
    }
}
