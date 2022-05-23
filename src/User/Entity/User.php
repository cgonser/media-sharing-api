<?php

namespace App\User\Entity;

use App\User\Repository\UserRepository;
use DateTime;
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
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'user_account')]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['email'])]
#[UniqueEntity(fields: ['username'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface, SoftDeletableInterface, TimestampableInterface
{
    use TimestampableTrait;
    use SoftDeletableTrait;

    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';

    #[ORM\Id, ORM\GeneratedValue('CUSTOM'), ORM\CustomIdGenerator(UuidGenerator::class)]
    #[ORM\Column(type: 'uuid', unique: true)]
    private UuidInterface $id;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $displayName = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $bio = null;

    #[Assert\NotBlank]
    #[Assert\Email]
    #[ORM\Column(unique: true)]
    private string $email;

    #[Assert\NotBlank]
    #[ORM\Column(unique: true)]
    private ?string $username = null;

    #[ORM\Column(nullable: true)]
    private ?string $password = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $phoneNumber = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $country = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $locale = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $timezone = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $profilePicture = null;

    #[ORM\Column(type: 'boolean', nullable: false, options: ['default' => true])]
    private bool $allowEmailMarketing = true;

    #[ORM\Column(options: ['default' => true])]
    private bool $isActive = true;

    #[ORM\Column(type: 'boolean', nullable: false, options: ['default' => false])]
    private bool $isTestUser = false;

    #[ORM\Column(type: 'boolean', nullable: false, options: ['default' => false])]
    private bool $isEmailValidated = false;

    #[ORM\Column(type: 'boolean', nullable: false, options: ['default' => false])]
    private bool $isProfilePrivate = false;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTimeInterface $emailValidatedAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTimeInterface $lastLoginAt = null;

    #[ORM\Column(type: 'boolean', nullable: false, options: ['default' => false])]
    private bool $isBlocked = false;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $adminNotes = null;

    #[ORM\OneToMany(mappedBy: 'following', targetEntity: UserFollow::class)]
    private Collection $followers;

    #[ORM\OneToMany(mappedBy: 'follower', targetEntity: UserFollow::class)]
    private Collection $following;

    #[ORM\Column(type: 'integer', nullable: false, options: ['default' => 0])]
    private int $followersCount = 0;

    #[ORM\Column(type: 'integer', nullable: false, options: ['default' => 0])]
    private int $followingCount = 0;

    #[ORM\Column(type: 'integer', nullable: false, options: ['default' => 0])]
    private int $videoCount = 0;

    public function __construct()
    {
        $this->followers = new ArrayCollection();
        $this->following = new ArrayCollection();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    public function setDisplayName(string $displayName): self
    {
        $this->displayName = $displayName;

        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): self
    {
        $this->bio = $bio;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(?string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function setTimezone(?string $timezone): self
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(?string $profilePicture): self
    {
        $this->profilePicture = $profilePicture;

        return $this;
    }

    public function allowEmailMarketing(): bool
    {
        return $this->allowEmailMarketing;
    }

    public function setAllowEmailMarketing(bool $allowEmailMarketing): self
    {
        $this->allowEmailMarketing = $allowEmailMarketing;

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

    public function isTestUser(): bool
    {
        return $this->isTestUser;
    }

    public function setIsTestUser(bool $isTestUser): self
    {
        $this->isTestUser = $isTestUser;

        return $this;
    }

    public function isEmailValidated(): bool
    {
        return $this->isEmailValidated;
    }

    public function setIsEmailValidated(bool $isEmailValidated): self
    {
        $this->isEmailValidated = $isEmailValidated;

        return $this;
    }

    public function getEmailValidatedAt(): ?DateTimeInterface
    {
        return $this->emailValidatedAt;
    }

    public function setEmailValidatedAt(?DateTimeInterface $emailValidatedAt): self
    {
        $this->emailValidatedAt = $emailValidatedAt;

        return $this;
    }

    public function isProfilePrivate(): bool
    {
        return $this->isProfilePrivate;
    }

    public function setIsProfilePrivate(bool $isProfilePrivate): self
    {
        $this->isProfilePrivate = $isProfilePrivate;

        return $this;
    }

    public function isBlocked(): bool
    {
        return $this->isBlocked;
    }

    public function setIsBlocked(bool $isBlocked): self
    {
        $this->isBlocked = $isBlocked;

        return $this;
    }

    public function getFollowersCount(): int
    {
        return $this->followersCount;
    }

    public function setFollowersCount(int $followersCount): self
    {
        $this->followersCount = $followersCount;

        return $this;
    }

    public function getFollowingCount(): int
    {
        return $this->followingCount;
    }

    public function setFollowingCount(int $followingCount): self
    {
        $this->followingCount = $followingCount;

        return $this;
    }

    public function getVideoCount(): int
    {
        return $this->videoCount;
    }

    public function setVideoCount(int $videoCount): self
    {
        $this->videoCount = $videoCount;

        return $this;
    }

    public function getFollowers(): ArrayCollection|Collection
    {
        return $this->followers;
    }

    public function getFollowing(): ArrayCollection|Collection
    {
        return $this->following;
    }

    public function getAdminNotes(): ?string
    {
        return $this->adminNotes;
    }

    public function appendAdminNotes(string $notes, ?DateTime $timestamp = null): self
    {
        if (null === $timestamp) {
            $timestamp = new DateTime();
        }

        $this->setAdminNotes(
            ltrim(
                $this->getAdminNotes().PHP_EOL.$timestamp->format(DateTimeInterface::ATOM).' - '.$notes,
                PHP_EOL
            )
        );

        return $this;
    }

    public function setAdminNotes(?string $adminNotes): self
    {
        $this->adminNotes = $adminNotes;

        return $this;
    }

    public function getLastLoginAt(): ?DateTimeInterface
    {
        return $this->lastLoginAt;
    }

    public function setLastLoginAt(?DateTimeInterface $lastLoginAt): self
    {
        $this->lastLoginAt = $lastLoginAt;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->getRoles(), true);
    }

    public function addRole(string $role): self
    {
        $this->roles[] = $role;
        $this->roles = array_unique($this->roles);

        return $this;
    }

    public function removeRole(string $roleName): void
    {
        $roles = $this->getRoles();
        foreach ($roles as $i => $role) {
            if ($role === $roleName) {
                unset($roles[$i]);
                break;
            }
        }

        $this->setRoles($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = array_unique($roles);

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->getEmail();
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {
    }

    public function __serialize(): array
    {
        return [$this->id, $this->email, $this->password];
    }

    public function __unserialize($serialized): void
    {
        // add $this->salt too if you don't use Bcrypt or Argon2i
        [$this->id, $this->email, $this->password] = unserialize($serialized, ['allowed_classes' => false]);
    }

    public function isNew(): bool
    {
        return !isset($this->id);
    }
}
