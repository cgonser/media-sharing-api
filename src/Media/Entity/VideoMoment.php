<?php

namespace App\Media\Entity;

use App\Media\Repository\VideoMomentRepository;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SoftDeletableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VideoMomentRepository::class)]
#[UniqueEntity(fields: ['video', 'moment'])]
class VideoMoment implements TimestampableInterface, SoftDeletableInterface
{
    use TimestampableTrait;
    use SoftDeletableTrait;

    #[ORM\Id, ORM\GeneratedValue('CUSTOM'), ORM\CustomIdGenerator(UuidGenerator::class)]
    #[ORM\Column(type: 'uuid', unique: true)]
    private UuidInterface $id;

    #[ORM\Column(type: 'uuid')]
    private UuidInterface $videoId;

    #[ORM\ManyToOne(targetEntity: Video::class, inversedBy: 'videoMoments')]
    private Video $video;

    #[ORM\Column(type: 'uuid')]
    private UuidInterface $momentId;

    #[ORM\ManyToOne(targetEntity: Moment::class)]
    private Moment $moment;

    #[Assert\NotNull]
    #[ORM\Column(type: 'integer', nullable: false)]
    private int $position;

    #[ORM\Column(nullable: false)]
    private int $duration;

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

    public function getMomentId(): UuidInterface
    {
        return $this->momentId;
    }

    public function setMomentId(UuidInterface $momentId): self
    {
        $this->momentId = $momentId;

        return $this;
    }

    public function getMoment(): Moment
    {
        return $this->moment;
    }

    public function setMoment(Moment $moment): self
    {
        $this->moment = $moment;
        $this->momentId = $moment->getId();

        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }
}
