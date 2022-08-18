<?php

namespace App\Tests\Api\Media;

use App\Tests\Api\AbstractApiTest;

abstract class AbstractMediaTest extends AbstractApiTest
{
    protected function getMomentDummyData(): array
    {
        return [
            'mood' => 'sad',
            'duration' => 3,
            'recordedAt' => (new \DateTime())->format(\DateTimeInterface::ATOM),
            'location' => [
                'long' => 42.34123,
                'lat' => 42.25243,
                'googlePlaceId' => 'XYZASD',
            ]
        ];
    }

    protected function getVideoDummyData(): array
    {
        return [
            'description' => 'Video Description',
            'moods' => [
                'happy',
                'excited',
                'funny',
            ],
            'recordedAt' => (new \DateTime())->format(\DateTimeInterface::ATOM),
        ];
    }
}
