<?php

namespace App\Media\Service;

use App\Core\Validation\EntityValidator;
use App\Media\Entity\Location;
use App\Media\Repository\LocationRepository;

class LocationManager
{
    public function __construct(
        private LocationRepository $locationRepository,
        private EntityValidator $validator,
    ) {
    }

    public function createOrRetrieveLocation(Location $location): Location
    {
        /** @var Location $existingLocation */
        $existingLocation = $this->locationRepository->findOneBy([
            'coordinates' => $location->getCoordinates(),
        ]);

        if (null !== $existingLocation) {
            return $existingLocation;
        }

        $this->create($location);

        return $location;
    }

    public function create(Location $location): void
    {
        $this->save($location);
    }

    public function update(Location $location): void
    {
        $this->save($location);
    }

    public function delete(object $location): void
    {
        $this->locationRepository->delete($location);
    }

    public function save(Location $location): void
    {
        $this->validator->validate($location);

        $this->locationRepository->save($location);
    }
}
