<?php

namespace App\Media\ResponseMapper;

use App\Media\Dto\LocationDto;
use App\Media\Dto\MomentDateDto;
use App\Media\Dto\MomentDto;
use App\Media\Entity\Moment;
use App\Media\Entity\MomentMediaItem;
use App\Media\Provider\MomentProvider;
use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;

class MomentResponseMapper
{
    public function __construct(
        private readonly MomentProvider $momentProvider,
    ) {
    }

    public function map(Moment $moment): MomentDto
    {
        $momentDto = new MomentDto();
        $momentDto->id = $moment->getId()->toString();
        $momentDto->userId = $moment->getUser()->getId()->toString();
        $momentDto->status = $moment->getStatus();
        $momentDto->mood = $moment->getMood();
        $momentDto->duration = $moment->getDuration();
        $momentDto->recordedOn = $moment->getRecordedAt()?->format('Y-m-d');
        $momentDto->recordedAt = $moment->getRecordedAt()?->format(DateTimeInterface::ATOM);
        $momentDto->mediaItems = !$moment->getMomentMediaItems()->isEmpty()
            ? $this->mapMediaItems($moment->getMomentMediaItems()->toArray())
            : null;

        if (null !== $moment->getLocationCoordinates()) {
            $locationDto = new LocationDto();
            $locationDto->lat = $moment->getLocationCoordinates()->getY();
            $locationDto->long = $moment->getLocationCoordinates()->getX();
            $locationDto->googlePlaceId = $moment->getLocationGooglePlaceId();
            $locationDto->address = $moment->getLocationAddress();

            $momentDto->location = $locationDto;
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
            $return[$momentMediaItem->getMediaItem()->getType()] = $momentMediaItem->getMediaItem()->getPublicUrl();
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
}
