<?php

namespace App\Media\Provider;

use App\Core\Provider\AbstractProvider;
use App\Media\Repository\LocationRepository;

class LocationProvider extends AbstractProvider
{
    public function __construct(LocationRepository $repository)
    {
        $this->repository = $repository;
    }
}
