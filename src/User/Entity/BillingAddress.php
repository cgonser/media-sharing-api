<?php

namespace App\User\Entity;

use App\User\Repository\BillingAddressRepository;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SoftDeletableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BillingAddressRepository::class)]
#[ORM\Table()]
class BillingAddress implements SoftDeletableInterface, TimestampableInterface
{
    use TimestampableTrait;
    use SoftDeletableTrait;

    #[ORM\Id, ORM\GeneratedValue('CUSTOM'), ORM\CustomIdGenerator(UuidGenerator::class)]
    #[ORM\Column(type: 'uuid', unique: true)]
    private UuidInterface $id;

    #[ORM\Column(type: 'uuid')]
    private UuidInterface $userId;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $companyName = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $email = null;

    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero()]
    #[Assert\Length(max: 3)]
    private ?string $phoneIntlCode = null;

    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero()]
    #[Assert\Length(max: 3)]
    private ?string $phoneAreaCode = null;

    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero()]
    #[Assert\Length(max: 20)]
    private ?string $phoneNumber = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $documentType = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $documentNumber = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $addressLine1 = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $addressLine2 = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $addressNumber = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $addressDistrict = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $addressCity = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $addressState = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $addressCountry = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $addressZipCode = null;

    public function getId(): UuidInterface
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

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(?string $companyName): self
    {
        $this->companyName = $companyName;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPhoneIntlCode(): ?string
    {
        return $this->phoneIntlCode;
    }

    public function setPhoneIntlCode(?string $phoneIntlCode): self
    {
        $this->phoneIntlCode = $phoneIntlCode;

        return $this;
    }

    public function getPhoneAreaCode(): ?string
    {
        return $this->phoneAreaCode;
    }

    public function setPhoneAreaCode(?string $phoneAreaCode): self
    {
        $this->phoneAreaCode = $phoneAreaCode;

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

    public function getDocumentType(): ?string
    {
        return $this->documentType;
    }

    public function setDocumentType(?string $documentType): self
    {
        $this->documentType = $documentType;

        return $this;
    }

    public function getDocumentNumber(): ?string
    {
        return $this->documentNumber;
    }

    public function setDocumentNumber(?string $documentNumber): self
    {
        $this->documentNumber = $documentNumber;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getAddressLine1(): ?string
    {
        return $this->addressLine1;
    }

    public function setAddressLine1(?string $addressLine1): self
    {
        $this->addressLine1 = $addressLine1;

        return $this;
    }

    public function getAddressLine2(): ?string
    {
        return $this->addressLine2;
    }

    public function setAddressLine2(?string $addressLine2): self
    {
        $this->addressLine2 = $addressLine2;

        return $this;
    }

    public function getAddressNumber(): ?string
    {
        return $this->addressNumber;
    }

    public function setAddressNumber(?string $addressNumber): self
    {
        $this->addressNumber = $addressNumber;

        return $this;
    }

    public function getAddressDistrict(): ?string
    {
        return $this->addressDistrict;
    }

    public function setAddressDistrict(?string $addressDistrict): self
    {
        $this->addressDistrict = $addressDistrict;

        return $this;
    }

    public function getAddressCity(): ?string
    {
        return $this->addressCity;
    }

    public function setAddressCity(?string $addressCity): self
    {
        $this->addressCity = $addressCity;

        return $this;
    }

    public function getAddressState(): ?string
    {
        return $this->addressState;
    }

    public function setAddressState(?string $addressState): self
    {
        $this->addressState = $addressState;

        return $this;
    }

    public function getAddressCountry(): ?string
    {
        return $this->addressCountry;
    }

    public function setAddressCountry(?string $addressCountry): self
    {
        $this->addressCountry = $addressCountry;

        return $this;
    }

    public function getAddressZipCode(): ?string
    {
        return $this->addressZipCode;
    }

    public function setAddressZipCode(?string $addressZipCode): self
    {
        $this->addressZipCode = $addressZipCode;

        return $this;
    }
}
