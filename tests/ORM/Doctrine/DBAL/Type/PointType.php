<?php

namespace App\Tests\ORM\Doctrine\DBAL\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType;
use LongitudeOne\Spatial\PHP\Types\Geometry\Point;

class PointType extends JsonType
{
    public function convertToPHPValue($value, AbstractPlatform $platform): ?Point
    {
        /** @var ?array{x: float, y: float} $data */
        $data = parent::convertToPHPValue($value, $platform);
        if (null === $data) {
            return null;
        }

        $x = $data['x'];
        $y = $data['y'];

        /** @psalm-suppress TooManyArguments */
        return new Point($x, $y);
    }

    /**
     * @param ?Point $value
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (!$value) {
            return parent::convertToDatabaseValue(null, $platform);
        }

        return parent::convertToDatabaseValue([
            'x' => $value->getLongitude(),
            'y' => $value->getLatitude(),
        ], $platform);
    }

    public function getName(): string
    {
        return 'point';
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}