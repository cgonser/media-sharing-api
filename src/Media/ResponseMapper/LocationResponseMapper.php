<?php

namespace App\Media\ResponseMapper;

use App\Media\Dto\LocationDto;
use App\Media\Entity\Location;

class LocationResponseMapper
{
    public function map(Location $location): LocationDto
    {
        $locationDto = new LocationDto();
        $locationDto->lat = $location->getCoordinates()->getY();
        $locationDto->long = $location->getCoordinates()->getX();
        $locationDto->googlePlaceId = $location->getGooglePlaceId();
        $locationDto->address = $location->getAddress();

        return $locationDto;
    }

    public function mapMultiple(array $locations): array
    {
        return array_map(fn ($location) => $this->map($location), $locations);
    }
}
