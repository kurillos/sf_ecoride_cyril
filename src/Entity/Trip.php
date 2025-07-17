<?php

namespace App\Entity;

use App\Repository\TripRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use App\Entity\Vehicle;

#[ORM\Entity(repositoryClass: TripRepository::class)]
class Trip
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $departureLocation = null;

    #[ORM\Column(length: 255)]
    private ?string $destinationLocation = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $departureTime = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $arrivalTime = null;

    #[ORM\Column]
    private ?int $availableSeats = null;

    #[ORM\Column]
    private ?float $pricePerSeat = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?bool $isSmokingAllowed = null;

    #[ORM\Column]
    private ?bool $areAnimalsAllowed = null;

    #[ORM\Column(length: 50)]
    private ?string $status = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'tripsAsDriver')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $driver = null;

    #[ORM\ManyToOne(targetEntity: Vehicle::class, inversedBy: 'trips')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Vehicle $vehicle = null;

    public function __construct()
    {
        $this->status = 'scheduled';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDepartureLocation(): ?string
    {
        return $this->departureLocation;
    }

    public function setDepartureLocation(string $departureLocation): static
    {
        $this->departureLocation = $departureLocation;

        return $this;
    }

    public function getDestinationLocation(): ?string
    {
        return $this->destinationLocation;
    }

    public function setDestinationLocation(string $destinationLocation): static
    {
        $this->destinationLocation = $destinationLocation;

        return $this;
    }

    public function getDepartureTime(): ?\DateTimeImmutable
    {
        return $this->departureTime;
    }

    public function setDepartureTime(\DateTimeImmutable $departureTime): static
    {
        $this->departureTime = $departureTime;

        return $this;
    }

    public function getArrivalTime(): ?\DateTimeImmutable
    {
        return $this->arrivalTime;
    }

    public function setArrivalTime(\DateTimeImmutable $arrivalTime): static
    {
        $this->arrivalTime = $arrivalTime;

        return $this;
    }

    public function getAvailableSeats(): ?int
    {
        return $this->availableSeats;
    }

    public function setAvailableSeats(int $availableSeats): static
    {
        $this->availableSeats = $availableSeats;

        return $this;
    }

    public function getPricePerSeat(): ?float
    {
        return $this->pricePerSeat;
    }

    public function setPricePerSeat(float $pricePerSeat): static
    {
        $this->pricePerSeat = $pricePerSeat;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function isSmokingAllowed(): ?bool
    {
        return $this->isSmokingAllowed;
    }

    public function setIsSmokingAllowed(bool $isSmokingAllowed): static
    {
        $this->isSmokingAllowed = $isSmokingAllowed;

        return $this;
    }

    public function isAreAnimalsAllowed(): ?bool
    {
        return $this->areAnimalsAllowed;
    }

    public function setAreAnimalsAllowed(bool $areAnimalsAllowed): static
    {
        $this->areAnimalsAllowed = $areAnimalsAllowed;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getDriver(): ?User
    {
        return $this->driver;
    }

    public function setDriver(?User $driver): static
    {
        $this->driver = $driver;

        return $this;
    }

    public function getVehicle(): ?Vehicle
    {
        return $this->vehicle;
    }

    public function setVehicle(?Vehicle $vehicle): static
    {
        $this->vehicle = $vehicle;

        return $this;
    }
}
