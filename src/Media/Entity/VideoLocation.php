<?php

namespace App\Media\Entity;

use App\Media\Repository\VideoLocationRepository;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SoftDeletableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity(repositoryClass: VideoLocationRepository::class)]
class VideoLocation implements TimestampableInterface, SoftDeletableInterface
{
    use TimestampableTrait;
    use SoftDeletableTrait;

    #[ORM\Id, ORM\GeneratedValue('CUSTOM'), ORM\CustomIdGenerator(UuidGenerator::class)]
    #[ORM\Column(type: 'uuid', unique: true)]
    private UuidInterface $id;

    #[ORM\Column(type: 'uuid')]
    private UuidInterface $videoId;

    #[ORM\ManyToOne(targetEntity: Video::class, inversedBy: 'videoLocations')]
    private Video $video;

    #[ORM\Column(type: 'uuid')]
    private UuidInterface $locationId;

    #[ORM\ManyToOne(targetEntity: Location::class)]
    private Location $location;

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getVideoId(): UuidInterface
    {
        return $this->videoId;
    }

    public function setVideoId(UuidInterface $videoId): self
    {
        $this->videoId = $videoId;

        return $this;
    }

    public function getVideo(): Video
    {
        return $this->video;
    }

    public function setVideo(Video $video): self
    {
        $this->video = $video;

        return $this;
    }

    public function getLocationId(): UuidInterface
    {
        return $this->locationId;
    }

    public function setLocationId(UuidInterface $locationId): self
    {
        $this->locationId = $locationId;

        return $this;
    }

    public function getLocation(): Location
    {
        return $this->location;
    }

    public function setLocation(Location $location): self
    {
        $this->location = $location;

        return $this;
    }
}
