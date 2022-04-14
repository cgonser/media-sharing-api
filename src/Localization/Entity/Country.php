<?php

namespace App\Localization\Entity;

use App\Localization\Repository\CountryRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CountryRepository::class)]
#[ORM\Table()]
#[UniqueEntity(fields: ['code'])]
class Country implements \Stringable
{
    #[ORM\Id, ORM\GeneratedValue('CUSTOM'), ORM\CustomIdGenerator(UuidGenerator::class)]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?UuidInterface $id = null;

    #[ORM\Column(type: 'uuid', nullable: true)]
    private ?UuidInterface $currencyId = null;

    #[ORM\ManyToOne(targetEntity: Currency::class)]
    private ?Currency $currency = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $primaryTimezone = null;

    #[ORM\Column(type: 'json')]
    private array $timezones = [];

    #[ORM\Column(type: 'string', nullable: false)]
    #[Assert\NotBlank]
    private string $name;

    #[ORM\Column(type: 'string', nullable: false)]
    #[Assert\NotBlank]
    private string $code;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $primaryLocale = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private bool $isActive = true;

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getCurrencyId(): UuidInterface
    {
        return $this->currencyId;
    }

    public function setCurrencyId(UuidInterface $currencyId): self
    {
        $this->currencyId = $currencyId;

        return $this;
    }

    public function getCurrency(): ?Currency
    {
        return $this->currency;
    }

    public function setCurrency(Currency $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getPrimaryTimezone(): ?string
    {
        return $this->primaryTimezone;
    }

    public function setPrimaryTimezone(?string $primaryTimezone): self
    {
        $this->primaryTimezone = $primaryTimezone;

        return $this;
    }

    public function getTimezones(): array
    {
        return $this->timezones;
    }

    public function setTimezones(array $timezones): self
    {
        $this->timezones = $timezones;

        return $this;
    }

    public function getPrimaryLocale(): ?string
    {
        return $this->primaryLocale;
    }

    public function setPrimaryLocale(string $primaryLocale): self
    {
        $this->primaryLocale = $primaryLocale;

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

    public function __toString(): string
    {
        return $this->getName();
    }

    public function isNew(): bool
    {
        return !isset($this->id);
    }
}
