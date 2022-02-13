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

#[ORM\Entity(repositoryClass: UserActivityRepository::class)]
#[ORM\Table()]
class UserReview implements TimestampableInterface, SoftDeletableInterface
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

    #[ORM\Column(type: 'uuid')]
    private UuidInterface $reviewerId;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private User $reviewer;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $review;

    #[ORM\Column(type: 'integer')]
    private string $rating;

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getUserId(): UuidInterface
    {
        return $this->userId;
    }

    public function setUserId(UuidInterface $userId): self
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

    public function getReviewerId(): UuidInterface
    {
        return $this->reviewerId;
    }

    public function setReviewerId(UuidInterface $reviewerId): self
    {
        $this->reviewerId = $reviewerId;

        return $this;
    }

    public function getReviewer(): User
    {
        return $this->reviewer;
    }

    public function setReviewer(User $reviewer): self
    {
        $this->reviewer = $reviewer;

        return $this;
    }

    public function getReview(): ?string
    {
        return $this->review;
    }

    public function setReview(?string $review): self
    {
        $this->review = $review;

        return $this;
    }

    public function getRating(): string
    {
        return $this->rating;
    }

    public function setRating(string $rating): self
    {
        $this->rating = $rating;

        return $this;
    }
}
