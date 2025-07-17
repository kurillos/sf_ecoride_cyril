<?php

namespace App\Entity;

use App\Repository\VehicleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VehicleRepository::class)]
class Vehicle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'vehicles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La marque ne peut pas être vide.")]
    private ?string $brand = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le modèle ne peut être vide.")]
    private ?string $model = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La couleur ne peut être vide.")]
    private ?string $color = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La plaque d'immatriculation est requise.")]
    private ?string $licensePlate = null;

    #[ORM\Column]
    #[Assert\Positive(message: "Le nombre de place doit être positif")]
    #[Assert\Range(min: 1, max: 9, notInRangeMessage: "Le nombre de place doit être ente 1 et 9.")]
    private ?int $seats = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le type de véhicule ne peut être vide.")]
    #[Assert\Choice(choices: ['citadine', 'berline', 'suv', 'break', 'utilitaire', 'other'], message: "Type de véhicule invalide.")]
    private ?string $type = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isElectric = false;

    public function getUser(): ?User
    {
        return $this->user;
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function getLicensePlate(): ?string
    {
        return $this->licensePlate;
    }

    public function getSeats(): ?int
    {
        return $this->seats;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function isElectric(): ?bool
    {
        return $this->isElectric;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function setBrand(string $brand): static
    {
        $this->brand = $brand;
        return $this;
    }

    public function setModel(string $model): static
    {
        $this->model = $model;
        return $this;
    }

    public function setColor(string $color): static
    {
        $this->color = $color;
        return $this;
    }

    public function setLicensePlate(string $licensePlate): static
    {
        $this->licensePlate = $licensePlate;
        return $this;
    }

    public function setSeats(int $seats): static
    {
        $this->seats = $seats;
        return $this;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }
 

    public function setIsElectric(bool $isElectric): static
    {
        $this->isElectric = $isElectric;
        return $this;
    }


}
