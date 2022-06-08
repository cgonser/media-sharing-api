<?php

namespace App\Media\Entity;

use App\Media\Enumeration\MediaItemType;
use App\Media\Repository\VideoMediaItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SoftDeletableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity(repositoryClass: VideoMediaItemRepository::class)]
class VideoMediaItem implements TimestampableInterface, SoftDeletableInterface
{
    use TimestampableTrait;
    use SoftDeletableTrait;

    public const REQUIRED_TYPES = [
        50 => [
            MediaItemType::IMAGE_THUMBNAIL,
            MediaItemType::VIDEO_LOW,
        ],
        10 => MediaItemType::VIDEO_HIGH,
        0 => MediaItemType::VIDEO_ORIGINAL,
    ];

    #[ORM\Id, ORM\GeneratedValue('CUSTOM'), ORM\CustomIdGenerator(UuidGenerator::class)]
    #[ORM\Column(type: 'uuid', unique: true)]
    private UuidInterface $id;

    #[ORM\Column(type: 'uuid')]
    private UuidInterface $videoId;

    #[ORM\ManyToOne(targetEntity: Video::class, inversedBy: 'videoMediaItems')]
    private Video $video;

    #[ORM\Column(type: 'uuid')]
    private UuidInterface $mediaItemId;

    #[ORM\ManyToOne(targetEntity: MediaItem::class)]
    private MediaItem $mediaItem;

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
        $this->videoId = $video->getId();

        return $this;
    }

    public function getMediaItemId(): UuidInterface
    {
        return $this->mediaItemId;
    }

    public function setMediaItemId(UuidInterface $mediaItemId): self
    {
        $this->mediaItemId = $mediaItemId;

        return $this;
    }

    public function getMediaItem(): MediaItem
    {
        return $this->mediaItem;
    }

    public function setMediaItem(MediaItem $mediaItem): self
    {
        $this->mediaItem = $mediaItem;

        return $this;
    }
}
