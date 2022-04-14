<?php

namespace App\Media\ResponseMapper;

use App\Media\Dto\MomentDto;
use App\Media\Entity\Moment;
use DateTimeInterface;

class MomentResponseMapper
{
    public function map(Moment $moment): MomentDto
    {
        $momentDto = new MomentDto();
        $momentDto->id = $moment->getId()->toString();
        $momentDto->userId = $moment->getUser()->getId()->toString();
        $momentDto->mood = $moment->getMood();
        $momentDto->location = $moment->getLocation();
        $momentDto->duration = $moment->getDuration();
        $momentDto->recordedAt = $moment->getRecordedAt()?->format(DateTimeInterface::ATOM);

        return $momentDto;
    }

    public function mapMultiple(array $moments): array
    {
        $momentDtos = [];

        foreach ($moments as $moment) {
            $momentDtos[] = $this->map($moment);
        }

        return $momentDtos;
    }
}
