<?php

namespace App\Localization\Entity;

use App\Localization\Repository\CurrencyRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CurrencyRepository::class)]
#[ORM\Table()]
#[UniqueEntity(fields: ['code'])]
class Currency implements \Stringable
{
    #[ORM\Id, ORM\GeneratedValue('CUSTOM'), ORM\CustomIdGenerator(UuidGenerator::class)]
    #[ORM\Column(type: 'uuid')]
    private ?UuidInterface $id = null;

    #[ORM\Column(type: 'string', nullable: false)]
    #[Assert\NotBlank()]
    private string $name;

    #[ORM\Column(type: 'string', nullable: false)]
    #[Assert\NotBlank()]
    private string $code;

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

    public function __toString(): string
    {
        return $this->getName();
    }

    public function isNew(): bool
    {
        return !isset($this->id);
    }
}
