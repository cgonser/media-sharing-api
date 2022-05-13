<?php

namespace App\Media\Entity;

use App\Media\Repository\LocationRepository;
use LongitudeOne\Spatial\PHP\Types\Geometry\Point;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SoftDeletableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Index(columns: ['coordinates'], name: 'idx_location_coordinates')]
#[ORM\Index(columns: ['google_place_id'], name: 'idx_location_google_place_id')]
#[ORM\Entity(repositoryClass: LocationRepository::class)]
class Location implements TimestampableInterface, SoftDeletableInterface
{
    use TimestampableTrait;
    use SoftDeletableTrait;

    #[ORM\Id, ORM\GeneratedValue('CUSTOM'), ORM\CustomIdGenerator(UuidGenerator::class)]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?UuidInterface $id = null;

    #[ORM\Column(type: 'point', nullable: true)]
    private ?Point $coordinates = null;

    #[ORM\Column(nullable: true)]
    private ?string $googlePlaceId = null;

    #[ORM\Column(nullable: true)]
    private ?string $address = null;

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getCoordinates(): ?Point
    {
        return $this->coordinates;
    }

    public function setCoordinates(?Point $coordinates): self
    {
        $this->coordinates = $coordinates;

        return $this;
    }

    public function getGooglePlaceId(): ?string
    {
        return $this->googlePlaceId;
    }

    public function setGooglePlaceId(?string $googlePlaceId): self
    {
        $this->googlePlaceId = $googlePlaceId;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }
}
