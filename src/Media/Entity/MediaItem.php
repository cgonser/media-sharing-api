<?php

namespace App\Media\Entity;

use App\Media\Repository\MediaItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SoftDeletableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity(repositoryClass: MediaItemRepository::class)]
class MediaItem implements TimestampableInterface, SoftDeletableInterface
{
    public const STATUS_UPLOAD_PENDING = 'upload_pending';
    public const STATUS_AVAILABLE = 'available';

    public const TYPE_VIDEO = 'video';
    public const TYPE_THUMBNAIL = 'thumbnail';

    public const TYPES = [
        self::TYPE_VIDEO,
        self::TYPE_THUMBNAIL,
    ];

    public const EXTENSIONS = [
        'jpeg',
        'jpg',
        'mp4',
        'mov',
        'png',
    ];

    use TimestampableTrait;
    use SoftDeletableTrait;

    #[ORM\Id, ORM\GeneratedValue('CUSTOM'), ORM\CustomIdGenerator(UuidGenerator::class)]
    #[ORM\Column(type: 'uuid', unique: true)]
    private UuidInterface $id;

    #[ORM\Column(nullable: false)]
    private string $status;

    #[ORM\Column(nullable: false)]
    private string $type;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $publicUrl = null;

    #[ORM\Column(nullable: true)]
    private ?string $filename = null;

    #[ORM\Column(nullable: true)]
    private ?string $extension = null;

    #[ORM\Column(nullable: true)]
    private ?string $mimeType = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $comments = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $uploadUrl = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $uploadUrlValidUntil = null;

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getPublicUrl(): ?string
    {
        return $this->publicUrl;
    }

    public function setPublicUrl(?string $publicUrl): self
    {
        $this->publicUrl = $publicUrl;

        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(?string $mimeType): self
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(?string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(?string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getComments(): ?string
    {
        return $this->comments;
    }

    public function setComment(?string $comments): self
    {
        $this->comments = $comments;

        return $this;
    }

    public function getUploadUrl(): ?string
    {
        return $this->uploadUrl;
    }

    public function setUploadUrl(?string $uploadUrl): self
    {
        $this->uploadUrl = $uploadUrl;

        return $this;
    }

    public function getUploadUrlValidUntil(): ?\DateTimeInterface
    {
        return $this->uploadUrlValidUntil;
    }

    public function setUploadUrlValidUntil(?\DateTimeInterface $uploadUrlValidUntil): self
    {
        $this->uploadUrlValidUntil = $uploadUrlValidUntil;

        return $this;
    }
}
