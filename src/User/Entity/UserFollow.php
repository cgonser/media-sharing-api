<?php

namespace App\User\Entity;

use App\User\Repository\UserActivityRepository;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SoftDeletableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: UserActivityRepository::class)]
#[UniqueEntity(fields: ['follower', 'following'])]
class UserFollow implements TimestampableInterface, SoftDeletableInterface
{
    use TimestampableTrait;
    use SoftDeletableTrait;

    #[ORM\Id, ORM\GeneratedValue('CUSTOM'), ORM\CustomIdGenerator(UuidGenerator::class)]
    #[ORM\Column(type: 'uuid', unique: true)]
    private UuidInterface $id;

    #[ORM\Column(type: 'uuid')]
    private UuidInterface $followerId;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private User $follower;

    #[ORM\Column(type: 'uuid')]
    private UuidInterface $followingId;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private User $following;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $isApproved;

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getFollowerId(): UuidInterface
    {
        return $this->followerId;
    }

    public function setFollowerId(UuidInterface $followerId): self
    {
        $this->followerId = $followerId;

        return $this;
    }

    public function getFollower(): User
    {
        return $this->follower;
    }

    public function setFollower(User $follower): self
    {
        $this->follower = $follower;

        return $this;
    }

    public function getFollowingId(): UuidInterface
    {
        return $this->followingId;
    }

    public function setFollowingId(UuidInterface $followingId): self
    {
        $this->followingId = $followingId;

        return $this;
    }

    public function getFollowing(): User
    {
        return $this->following;
    }

    public function setFollowing(User $following): self
    {
        $this->following = $following;

        return $this;
    }

    public function isApproved(): ?bool
    {
        return $this->isApproved;
    }

    public function setIsApproved(?bool $isApproved): self
    {
        $this->isApproved = $isApproved;

        return $this;
    }
}
