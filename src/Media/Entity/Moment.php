<?php

namespace App\Media\Entity;

use App\Media\Repository\MomentRepository;
use App\User\Entity\User;
use CrEOF\Spatial\PHP\Types\Geography\Point;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SoftDeletableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MomentRepository::class)]
class Moment implements TimestampableInterface, SoftDeletableInterface
{
    use TimestampableTrait;
    use SoftDeletableTrait;

    #[ORM\Id, ORM\GeneratedValue('CUSTOM'), ORM\CustomIdGenerator(UuidGenerator::class)]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?UuidInterface $id = null;

    #[ORM\Column(type: 'uuid')]
    private ?UuidInterface $userId = null;

    #[ORM\ManyToOne]
    #[Assert\NotNull]
    private User $user;

    #[ORM\Column(nullable: true)]
    private ?string $mood = null;

    #[ORM\Column(type: 'point', nullable: true)]
    private ?Point $location = null;

    #[ORM\Column(nullable: true)]
    private ?int $duration = null;

    #[ORM\Column(type: "datetime", nullable: false)]
    #[Assert\NotNull]
    private ?DateTimeInterface $recordedAt = null;

    #[ORM\Column(type: "date", nullable: false)]
    #[Assert\NotNull]
    private ?DateTimeInterface $recordedOn = null;

    #[ORM\OneToMany(mappedBy: 'moment', targetEntity: MomentMediaItem::class, cascade: ["persist"])]
    private Collection $momentMediaItems;

    public function __construct()
    {
        $this->momentMediaItems = new ArrayCollection();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getUserId(): ?UuidInterface
    {
        return $this->userId;
    }

    public function setUserId(?UuidInterface $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getMood(): ?string
    {
        return $this->mood;
    }

    public function setMood(?string $mood): self
    {
        $this->mood = $mood;

        return $this;
    }

    public function getLocation(): ?Point
    {
        return $this->location;
    }

    public function setLocation(?Point $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getRecordedAt(): ?DateTimeInterface
    {
        return $this->recordedAt;
    }

    public function setRecordedAt(?DateTimeInterface $recordedAt): self
    {
        $this->recordedAt = $recordedAt;
        $this->setRecordedOn($recordedAt);

        return $this;
    }

    public function getRecordedOn(): ?DateTimeInterface
    {
        return $this->recordedOn;
    }

    public function setRecordedOn(?DateTimeInterface $recordedOn): self
    {
        $this->recordedOn = $recordedOn;

        return $this;
    }

    public function getMomentMediaItems(): Collection
    {
        return $this->momentMediaItems;
    }
}
