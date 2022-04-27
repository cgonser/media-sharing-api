<?php

namespace App\Media\ResponseMapper;

use App\Media\Dto\MomentDto;
use App\Media\Entity\Moment;
use App\Media\Service\MomentMediaItemManager;
use DateTimeInterface;
use Doctrine\Common\Collections\Collection;

class MomentResponseMapper
{
    public function __construct(
        private MediaItemResponseMapper $mediaItemResponseMapper,
        private MomentMediaItemManager $momentMediaItemManager,
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
        $momentDto->mediaItems = $this->mapMediaItems(
            $this->momentMediaItemManager->extractActiveMediaItems($moment->getMomentMediaItems())
        );

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
        return array_map(
            fn ($momentMediaItem) => $this->mediaItemResponseMapper->map($momentMediaItem->getMediaItem()),
            $momentMediaItems
        );
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
}
