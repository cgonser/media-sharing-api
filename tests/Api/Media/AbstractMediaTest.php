<?php

namespace App\Tests\Api\Media;

use App\Tests\Api\AbstractApiTest;

abstract class AbstractMediaTest extends AbstractApiTest
{
    protected function getMomentDummyData(): array
    {
        return [
            'location' => 'Luxembourg',
            'mood' => 'sad',
            'duration' => 3,
            'recordedAt' => (new \DateTime())->format(\DateTimeInterface::ATOM),
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
            'locations' => [
                'Luxembourg',
                'Italy',
            ],
            'duration' => 9,
            'recordedAt' => (new \DateTime())->format(\DateTimeInterface::ATOM),
        ];
    }
}
