<?php

namespace App\Entity;

use App\Repository\UserPreferenceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: UserPreferenceRepository::class)]
class UserPreference
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private ?bool $isSmoker = false;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private ?bool $acceptsAnimals = false;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(max: 500, maxMessage: 'Les informations supplémentaires ne peuvent dépasser {{ limit }} caractère')]
    private ?string $additionalInfo = null;

    #[ORM\OneToOne(inversedBy: 'userPreference', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable:false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

     public function isSmoker(): ?bool
    {
        return $this->isSmoker;
    }

    public function setIsSmoker(bool $isSmoker): static
    {
        $this->isSmoker = $isSmoker;
        return $this;
    }

    public function isAcceptsAnimals(): ?bool
    {
        return $this->acceptsAnimals;
    }

    public function setAcceptsAnimals(bool $acceptsAnimals): static
    {
        $this->acceptsAnimals = $acceptsAnimals;
        return $this;
    }

    public function getAdditionalInfo(): ?string
    {
        return $this->additionalInfo;
    }

    public function setAdditionalInfo(?string $additionalInfo): static
    {
        $this->additionalInfo = $additionalInfo;
        return $this;
    }
}
