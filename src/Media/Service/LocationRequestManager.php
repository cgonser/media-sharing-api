<?php

namespace App\Media\Service;

use App\Media\Entity\Location;
use App\Media\Request\LocationRequest;
use LongitudeOne\Spatial\PHP\Types\Geometry\Point;

class LocationRequestManager
{
    public function __construct(
        private readonly LocationManager $locationManager,
    ) {
    }

    public function createFromRequest(LocationRequest $locationRequest): Location
    {
        $location = new Location();

        $this->mapFromRequest($location, $locationRequest);

        return $this->locationManager->createOrRetrieveLocation($location);
    }

    public function updateFromRequest(Location $location, LocationRequest $locationRequest): void
    {
        $this->mapFromRequest($location, $locationRequest);

        $this->locationManager->update($location);
    }

    public function mapFromRequest(Location $location, LocationRequest $locationRequest): void
    {
        if ($locationRequest->has('long') && $locationRequest->has('lat')) {
            $location->setCoordinates(
                new Point(
                    $locationRequest->long,
                    $locationRequest->lat,
                )
            );
        }

        if ($locationRequest->has('googlePlaceId')) {
            $location->setGooglePlaceId($locationRequest->googlePlaceId);
        }

        if ($locationRequest->has('address')) {
            $location->setAddress($locationRequest->address);
        }
    }
}
