<?php

namespace App\Media\Entity;

use App\Media\Enumeration\VideoStatus;
use App\Media\Repository\VideoRepository;
use App\User\Entity\User;
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

#[ORM\Entity(repositoryClass: VideoRepository::class)]
class Video implements TimestampableInterface, SoftDeletableInterface
{
    use TimestampableTrait;
    use SoftDeletableTrait;

    #[ORM\Id, ORM\GeneratedValue('CUSTOM'), ORM\CustomIdGenerator(UuidGenerator::class)]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?UuidInterface $id = null;

    #[ORM\Column(type: 'uuid')]
    private ?UuidInterface $userId = null;

    #[ORM\ManyToOne]
    #[Assert\NotBlank]
    private User $user;

    #[ORM\Column(type: 'uuid', nullable: true)]
    private ?UuidInterface $musicId = null;

    #[ORM\ManyToOne]
    private ?Music $music = null;

    #[ORM\Column(type: 'string', nullable: false, enumType: VideoStatus::class)]
    private VideoStatus $status;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'jsonb', nullable: true)]
    private ?array $moods = null;

    #[ORM\Column(nullable: true)]
    private ?int $duration = null;

    #[ORM\Column(nullable: true)]
    private ?int $likes = null;

    #[ORM\Column(nullable: true)]
    private ?int $comments = null;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTimeInterface $recordedAt = null;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTimeInterface $publishedAt = null;

    #[ORM\OneToMany(mappedBy: 'video', targetEntity: VideoLocation::class, cascade: ["persist"])]
    private Collection $videoLocations;

    #[ORM\OneToMany(mappedBy: 'video', targetEntity: VideoMoment::class, cascade: ["persist"])]
    private Collection $videoMoments;

    #[ORM\OneToMany(mappedBy: 'video', targetEntity: VideoLike::class, cascade: ["persist"])]
    private Collection $videoLikes;

    #[ORM\OneToMany(mappedBy: 'video', targetEntity: VideoComment::class, cascade: ["persist"])]
    private Collection $videoComments;

    #[ORM\OneToMany(mappedBy: 'video', targetEntity: VideoMediaItem::class, cascade: ["persist"])]
    private Collection $videoMediaItems;

    #[ORM\Column(type: 'boolean', nullable: false, options: ['default' => true])]
    private bool $overrideMomentsAudio = true;

    public function __construct()
    {
        $this->videoLocations = new ArrayCollection();
        $this->videoMoments = new ArrayCollection();
        $this->videoLikes = new ArrayCollection();
        $this->videoComments = new ArrayCollection();
        $this->videoMediaItems = new ArrayCollection();
        $this->status = VideoStatus::PENDING;
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

    public function getStatus(): VideoStatus
    {
        return $this->status;
    }

    public function setStatus(VideoStatus|string $status): self
    {
        $this->status = $status instanceOf VideoStatus ? $status : VideoStatus::from($status);

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getMoods(): ?array
    {
        return $this->moods;
    }

    public function setMoods(?array $moods): self
    {
        $this->moods = $moods;

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

    public function getLikes(): ?int
    {
        return $this->likes;
    }

    public function setLikes(?int $likes): self
    {
        $this->likes = $likes;

        return $this;
    }

    public function getComments(): ?int
    {
        return $this->comments;
    }

    public function setComments(?int $comments): self
    {
        $this->comments = $comments;

        return $this;
    }

    public function getRecordedAt(): ?\DateTimeInterface
    {
        return $this->recordedAt;
    }

    public function setRecordedAt(?\DateTimeInterface $recordedAt): self
    {
        $this->recordedAt = $recordedAt;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeInterface $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    public function getVideoLocations(): Collection
    {
        return $this->videoLocations;
    }

    public function addVideoLocation(VideoLocation $videoLocation): self
    {
        $videoLocation->setVideo($this);

        $this->videoLocations->add($videoLocation);

        return $this;
    }

    public function removeVideoLocation(VideoLocation $videoLocation): void
    {
        if ($this->videoLocations->contains($videoLocation)) {
            $this->videoLocations->removeElement($videoLocation);
        }
    }

    public function addLocation(Location $location): self
    {
        $videoLocation = new VideoLocation();
        $videoLocation->setVideo($this);
        $videoLocation->setLocation($location);

        $this->videoLocations->add($videoLocation);

        return $this;
    }

    /** Location[] */
    public function setLocations(array $locations): self
    {
        foreach ($locations as $location) {
            if (!$this->hasLocation($location)) {
                $this->addLocation($location);
            }
        }

        foreach ($this->getVideoLocations() as $videoLocation) {
            if (!in_array($videoLocation->getLocation(), $locations)) {
                $this->removeVideoLocation($videoLocation);
            }
        }

        return $this;
    }

    public function hasLocation(Location $location): bool
    {
        return !$this->videoLocations->filter(
            fn (VideoLocation $videoLocation) => $location->getId()->equals($videoLocation->getLocationId())
        )->isEmpty();
    }

    public function removeLocation(Location $location): void
    {
        /** @var VideoLocation $videoLocation */
        foreach ($this->videoLocations as $videoLocation) {
            if ($videoLocation->getLocationId()->equals($location->getId())) {
                $this->videoLocations->removeElement($videoLocation);
            }
        }
    }

    public function getMusicId(): ?UuidInterface
    {
        return $this->musicId;
    }

    public function setMusicId(?UuidInterface $musicId): self
    {
        $this->musicId = $musicId;

        return $this;
    }

    public function getMusic(): ?Music
    {
        return $this->music;
    }

    public function setMusic(?Music $music): self
    {
        $this->music = $music;

        return $this;
    }

    public function overrideMomentsAudio(): bool
    {
        return $this->overrideMomentsAudio;
    }

    public function setOverrideMomentsAudio(bool $overrideMomentsAudio): self
    {
        $this->overrideMomentsAudio = $overrideMomentsAudio;

        return $this;
    }

    public function getVideoMoments(): Collection
    {
        return $this->videoMoments;
    }

    public function addVideoMoment(VideoMoment $videoMoment): self
    {
        $videoMoment->setVideo($this);

        $this->videoMoments->add($videoMoment);

        return $this;
    }

    public function removeVideoMoment(VideoMoment $videoMoment): void
    {
        if ($this->videoMoments->contains($videoMoment)) {
            $this->videoMoments->removeElement($videoMoment);
        }
    }

    public function addMoment(Moment $moment, int $position): self
    {
        $videoMoment = new VideoMoment();
        $videoMoment->setVideo($this);
        $videoMoment->setMoment($moment);
        $videoMoment->setPosition($position);
        $videoMoment->setDuration($moment->getDuration());

        $this->videoMoments->add($videoMoment);

        return $this;
    }

    public function hasMoment(Moment $moment): bool
    {
        return !$this->videoMoments->filter(
            fn (VideoMoment $videoMoment) => $moment->getId()->equals($videoMoment->getMomentId())
        )->isEmpty();
    }

    public function updateMoment(Moment $moment, int $position): void
    {
        $videoMoment = $this->videoMoments->filter(
            fn (VideoMoment $videoMoment) => $moment->getId()->equals($videoMoment->getMomentId())
        )->first();

        if (null === $videoMoment) {
            return;
        }

        $videoMoment->position = $position;

        $this->videoMoments->set(
            $this->videoMoments->indexOf($videoMoment),
            $videoMoment
        );
    }

    public function removeMoment(Moment $moment): void
    {
        /** @var VideoMoment $videoMoment */
        foreach ($this->videoMoments as $videoMoment) {
            if ($videoMoment->getMomentId()->equals($moment->getId())) {
                $this->videoMoments->removeElement($videoMoment);
            }
        }
    }

    public function getVideoLikes(): Collection
    {
        return $this->videoLikes;
    }

    public function getVideoComments(): Collection
    {
        return $this->videoComments;
    }

    public function getVideoMediaItems(): Collection
    {
        return $this->videoMediaItems;
    }
}
