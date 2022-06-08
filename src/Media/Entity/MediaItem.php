<?php

namespace App\Media\Entity;

use App\Media\Enumeration\MediaItemExtension;
use App\Media\Enumeration\MediaItemStatus;
use App\Media\Enumeration\MediaItemType;
use App\Media\Repository\MediaItemRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SoftDeletableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MediaItemRepository::class)]
class MediaItem implements TimestampableInterface, SoftDeletableInterface
{
    use TimestampableTrait;
    use SoftDeletableTrait;

    #[ORM\Id, ORM\GeneratedValue('CUSTOM'), ORM\CustomIdGenerator(UuidGenerator::class)]
    #[ORM\Column(type: 'uuid', unique: true)]
    private UuidInterface $id;

    #[Assert\Type(MediaItemStatus::class)]
    #[ORM\Column(type: 'string', nullable: false, enumType: MediaItemStatus::class)]
    private ?MediaItemStatus $status = null;

    #[Assert\Type(MediaItemType::class)]
    #[ORM\Column(type: 'string', nullable: false, enumType: MediaItemType::class)]
    private MediaItemType $type;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $publicUrl = null;

    #[ORM\Column(unique: true, nullable: true)]
    private ?string $filename = null;

    #[Assert\Type(MediaItemExtension::class)]
    #[ORM\Column(type: 'string', nullable: false, enumType: MediaItemExtension::class)]
    private ?MediaItemExtension $extension = null;

    #[ORM\Column(nullable: true)]
    private ?string $mimeType = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $comments = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $uploadUrl = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTimeInterface $uploadUrlValidUntil = null;

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getStatus(): ?MediaItemStatus
    {
        return $this->status;
    }

    public function setStatus(MediaItemStatus|string $status): self
    {
        $this->status = $status instanceOf MediaItemStatus ? $status : MediaItemStatus::from($status);

        return $this;
    }

    public function getType(): MediaItemType
    {
        return $this->type;
    }

    public function setType(MediaItemType|string $type): self
    {
        $this->type = $type instanceOf MediaItemType ? $type : MediaItemType::from($type);

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

    public function getExtension(): ?MediaItemExtension
    {
        return $this->extension;
    }

    public function setExtension(MediaItemExtension|string $extension): self
    {
        $this->extension = $extension instanceOf MediaItemExtension ? $extension : MediaItemExtension::from($extension);

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

    public function getUploadUrlValidUntil(): ?DateTimeInterface
    {
        return $this->uploadUrlValidUntil;
    }

    public function setUploadUrlValidUntil(?DateTimeInterface $uploadUrlValidUntil): self
    {
        $this->uploadUrlValidUntil = $uploadUrlValidUntil;

        return $this;
    }
}
