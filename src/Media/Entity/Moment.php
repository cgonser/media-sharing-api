<?php

namespace App\Media\Entity;

use App\Media\Enumeration\MomentStatus;
use App\Media\Enumeration\Mood;
use App\Media\Repository\MomentRepository;
use App\User\Entity\User;
use LongitudeOne\Spatial\PHP\Types\Geometry\Point;
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

#[ORM\Index(columns: ['location_coordinates'], name: 'idx_moment_location_coordinates')]
#[ORM\Index(columns: ['location_google_place_id'], name: 'idx_moment_location_google_place_id')]
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

    #[ORM\Column(type: 'string', nullable: false, enumType: MomentStatus::class)]
    private MomentStatus $status;

    #[ORM\Column(type: 'string', nullable: false, enumType: Mood::class)]
    private Mood $mood;

    #[ORM\Column(type: 'point', nullable: true)]
    private ?Point $locationCoordinates = null;

    #[ORM\Column(nullable: true)]
    private ?string $locationGooglePlaceId = null;

    #[ORM\Column(nullable: true)]
    private ?string $locationAddress = null;

    #[Assert\NotNull]
    #[ORM\Column(type: 'decimal', nullable: false, options: ['precision' => 5, 'scale' => 2])]
    private float $duration;

    #[ORM\Column(type: "datetime", nullable: false)]
    #[Assert\NotNull]
    private ?DateTimeInterface $recordedAt = null;

    #[ORM\Column(type: "date", nullable: false)]
    #[Assert\NotNull]
    private ?DateTimeInterface $recordedOn = null;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?DateTimeInterface $publishedAt = null;

    #[ORM\OneToMany(mappedBy: 'moment', targetEntity: MomentMediaItem::class, cascade: ["persist"])]
    private Collection $momentMediaItems;

    public function __construct()
    {
        $this->momentMediaItems = new ArrayCollection();
        $this->status = MomentStatus::PENDING;
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
        $this->userId = $user->getId();

        return $this;
    }

    public function getStatus(): MomentStatus
    {
        return $this->status;
    }

    public function setStatus(MomentStatus|string $status): self
    {
        $this->status = $status instanceOf MomentStatus ? $status : MomentStatus::from($status);

        return $this;
    }

    public function getMood(): Mood
    {
        return $this->mood;
    }

    public function setMood(Mood|string $mood): self
    {
        $this->mood = $mood instanceOf Mood ? $mood : Mood::from($mood);

        return $this;
    }

    public function getLocationCoordinates(): ?Point
    {
        return $this->locationCoordinates;
    }

    public function setLocationCoordinates(?Point $locationCoordinates): self
    {
        $this->locationCoordinates = $locationCoordinates;

        return $this;
    }

    public function getLocationGooglePlaceId(): ?string
    {
        return $this->locationGooglePlaceId;
    }

    public function setLocationGooglePlaceId(?string $locationGooglePlaceId): self
    {
        $this->locationGooglePlaceId = $locationGooglePlaceId;

        return $this;
    }

    public function getLocationAddress(): ?string
    {
        return $this->locationAddress;
    }

    public function setLocationAddress(?string $locationAddress): self
    {
        $this->locationAddress = $locationAddress;

        return $this;
    }

    public function getDuration(): float
    {
        return $this->duration;
    }

    public function setDuration(float $duration): self
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

    public function getPublishedAt(): ?DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?DateTimeInterface $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    public function getMomentMediaItems(): Collection
    {
        return $this->momentMediaItems;
    }
}
