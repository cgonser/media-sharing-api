<?php

namespace App\User\Entity;

use App\User\Repository\UserSettingRepository;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SoftDeletableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity(repositoryClass: UserSettingRepository::class)]
#[ORM\Table()]
class UserSetting implements SoftDeletableInterface, TimestampableInterface
{
    use TimestampableTrait;
    use SoftDeletableTrait;

    #[ORM\Id, ORM\GeneratedValue('CUSTOM'), ORM\CustomIdGenerator(UuidGenerator::class)]
    #[ORM\Column(type: 'uuid', unique: true)]
    private UuidInterface $id;

    #[ORM\Column(type: 'uuid', nullable: true)]
    private ?UuidInterface $userId = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $value = null;

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getUserId(): ?UuidInterface
    {
        return $this->userId;
    }

    public function setUserId(?UuidInterface $userId): void
    {
        $this->userId = $userId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): UserSetting
    {
        $this->name = $name;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): UserSetting
    {
        $this->value = $value;

        return $this;
    }

    public function isNew(): bool
    {
        return !isset($this->id);
    }
}
