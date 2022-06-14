<?php

namespace App\Media\Entity;

use App\Media\Repository\MusicRepository;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SoftDeletableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity(repositoryClass: MusicRepository::class)]
class Music implements TimestampableInterface, SoftDeletableInterface
{
    use TimestampableTrait;
    use SoftDeletableTrait;

    #[ORM\Id, ORM\GeneratedValue('CUSTOM'), ORM\CustomIdGenerator(UuidGenerator::class)]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?UuidInterface $id = null;

    #[ORM\Column(nullable: false)]
    private ?string $displayName = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $filename = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $publicUrl = null;

    #[ORM\Column(type: 'decimal', nullable: true, options: ['scale' => 2, 'precision' => 6])]
    private ?float $duration = null;

    #[ORM\Column(type: 'boolean', nullable: false, options: ['default' => true])]
    private bool $isActive = true;

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    public function setDisplayName(?string $displayName): self
    {
        $this->displayName = $displayName;

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

    public function getPublicUrl(): ?string
    {
        return $this->publicUrl;
    }

    public function setPublicUrl(?string $publicUrl): self
    {
        $this->publicUrl = $publicUrl;

        return $this;
    }

    public function getDuration(): ?float
    {
        return $this->duration;
    }

    public function setDuration(?float $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }
}
