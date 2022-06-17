<?php

namespace App\Notification\Entity;

use App\Notification\Enumeration\NotificationChannel;
use App\Notification\Repository\UserNotificationChannelRepository;
use App\User\Entity\User;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SoftDeletableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity(repositoryClass: UserNotificationChannelRepository::class)]
#[ORM\Index(fields: ["userId", "channel", "deviceType"])]
#[ORM\UniqueConstraint(fields: ["userId", "channel", "deviceType", "isActive", "deletedAt"])]
class UserNotificationChannel implements SoftDeletableInterface, TimestampableInterface
{
    use TimestampableTrait;
    use SoftDeletableTrait;

    #[ORM\Id, ORM\GeneratedValue('CUSTOM'), ORM\CustomIdGenerator(UuidGenerator::class)]
    #[ORM\Column(type: 'uuid', unique: true)]
    private UuidInterface $id;

    #[ORM\Column(type: 'uuid')]
    private UuidInterface $userId;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private User $user;

    #[ORM\Column(type: 'string', nullable: false, enumType: NotificationChannel::class)]
    private NotificationChannel $channel;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $deviceType = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $externalId = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $token = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTimeInterface $expiresAt = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $details = null;

    #[ORM\Column(type: 'boolean', nullable: false, options: ['default' => true])]
    private bool $isActive = true;

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getUserId(): UuidInterface
    {
        return $this->userId;
    }

    public function setUserId(UuidInterface $userId): void
    {
        $this->userId = $userId;
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

    public function getChannel(): NotificationChannel
    {
        return $this->channel;
    }

    public function setChannel(NotificationChannel|string $channel): self
    {
        $this->channel = $channel instanceOf NotificationChannel ? $channel : NotificationChannel::from($channel);

        return $this;
    }

    public function getDeviceType(): ?string
    {
        return $this->deviceType;
    }

    public function setDeviceType(?string $deviceType): self
    {
        $this->deviceType = $deviceType;

        return $this;
    }

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    public function setExternalId(?string $externalId): self
    {
        $this->externalId = $externalId;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getExpiresAt(): ?DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(?DateTimeInterface $expiresAt): self
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    public function getDetails(): ?array
    {
        return $this->details;
    }

    public function setDetails(?array $details): self
    {
        $this->details = $details;

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

    public function isNew(): bool
    {
        return !isset($this->id);
    }
}
