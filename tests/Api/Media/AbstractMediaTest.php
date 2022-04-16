<?php

namespace App\Tests\Api\Media;

use App\Tests\Api\AbstractApiTest;

abstract class AbstractMediaTest extends AbstractApiTest
{
    protected function getMomentDummyData(): array
    {
        return [
            'mood' => 'happy',
            'location' => 'Luxembourg',
            'duration' => 3,
            'recordedAt' => (new \DateTime())->format(\DateTimeInterface::ATOM),
        ];
    }

    protected function getVideoDummyData(): array
    {
        return [
            'description' => 'Video Description',
            'mood' => 'happy',
            'locations' => [
                'Luxembourg',
                'Italy',
            ],
            'duration' => 9,
            'recordedAt' => (new \DateTime())->format(\DateTimeInterface::ATOM),
        ];
    }
}
