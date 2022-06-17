<?php

namespace App\Media\Entity;

use App\Media\Enumeration\MediaItemType;
use App\Media\Repository\MomentMediaItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SoftDeletableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity(repositoryClass: MomentMediaItemRepository::class)]
class MomentMediaItem implements TimestampableInterface, SoftDeletableInterface
{
    use TimestampableTrait;
    use SoftDeletableTrait;

    #[ORM\Id, ORM\GeneratedValue('CUSTOM'), ORM\CustomIdGenerator(UuidGenerator::class)]
    #[ORM\Column(type: 'uuid', unique: true)]
    private UuidInterface $id;

    #[ORM\Column(type: 'uuid')]
    private UuidInterface $momentId;

    #[ORM\ManyToOne(targetEntity: Moment::class, inversedBy: 'momentMediaItems')]
    private Moment $moment;

    #[ORM\Column(type: 'uuid')]
    private ?UuidInterface $mediaItemId = null;

    #[ORM\ManyToOne(targetEntity: MediaItem::class)]
    private ?MediaItem $mediaItem = null;

    public function getId(): ?UuidInterface
    {
        return $this->id;
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

    public function getMediaItemId(): ?UuidInterface
    {
        return $this->mediaItemId;
    }

    public function setMediaItemId(UuidInterface $mediaItemId): self
    {
        $this->mediaItemId = $mediaItemId;

        return $this;
    }

    public function getMediaItem(): ?MediaItem
    {
        return $this->mediaItem;
    }

    public function setMediaItem(MediaItem $mediaItem): self
    {
        $this->mediaItem = $mediaItem;
        $this->mediaItemId = $mediaItem->getId();

        return $this;
    }
}
