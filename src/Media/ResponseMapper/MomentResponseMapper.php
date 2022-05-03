<?php

namespace App\Media\ResponseMapper;

use App\Media\Dto\MomentDateDto;
use App\Media\Dto\MomentDto;
use App\Media\Entity\Moment;
use App\Media\Entity\MomentMediaItem;
use App\Media\Service\MomentMediaItemManager;
use DateTimeInterface;

class MomentResponseMapper
{
    public function __construct(
        private readonly MomentMediaItemManager $momentMediaItemManager,
    ) {
    }

    public function map(Moment $moment): MomentDto
    {
        $momentDto = new MomentDto();
        $momentDto->id = $moment->getId()->toString();
        $momentDto->userId = $moment->getUser()->getId()->toString();
        $momentDto->mood = $moment->getMood();
        $momentDto->location = $moment->getLocation();
        $momentDto->duration = $moment->getDuration();
        $momentDto->recordedOn = $moment->getRecordedAt()?->format('Y-m-d');
        $momentDto->recordedAt = $moment->getRecordedAt()?->format(DateTimeInterface::ATOM);
        $momentDto->mediaItems = !$moment->getMomentMediaItems()->isEmpty()
            ? $this->mapMediaItems($this->momentMediaItemManager->extractActiveMediaItems($moment->getMomentMediaItems()))
            : null;

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

    public function mapRecordedOnDates(array $results): array
    {
        $return = [];

        foreach ($results as $result) {
            $momentDateDto = new MomentDateDto();
            $momentDateDto->recordedOn = $result['recordedOn']->format('Y-m-d');
            $momentDateDto->count = $result['count'];

            $return[] = $momentDateDto;
        }

        return $return;
    }
}
