<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use App\Entity\Vehicle;
use App\Entity\Review;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[Vich\Uploadable]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

     #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $credits = 0;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le prénom ne peut pas être vide.')]
    #[Assert\Length(min: 2, max: 255, minMessage: 'Le prénom doit contenir au moins {{ limit }} caractères.')]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom ne peut pas être vide.')]
    #[Assert\Length(min: 2, max: 255, minMessage: 'Le nom doit contenir au moins {{ limit }} caractères.')]
    private ?string $lastName = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: 'Le pseudonyme ne peut pas être vide.')]
    #[Assert\Length(min: 3, max: 255, minMessage: 'Le pseudonyme doit contenir au moins {{ limit }} caractères.')]
    private ?string $pseudo = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[Vich\UploadableField(mapping: 'profile_picture', fileNameProperty: 'profilePictureFilename')]
    private ?File $profilePictureFile = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $profilePictureFilename = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?UserPreference $userPreference = null;

    #[ORM\OneToMany(targetEntity: Vehicle::class, mappedBy: 'owner', orphanRemoval: true, cascade: ['persist'])]
    private Collection $vehicles;

    #[ORM\Column(length: 255, options: ['default' => 'passenger'])]
    private ?string $desiredRole = 'passenger';

    #[ORM\OneToMany(mappedBy: 'driver', targetEntity: Trip::class)]
    private Collection $tripAsDriver;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Review::class)]
    private Collection $reviewsGiven;

    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'ratedDriver')]
    private Collection $reviewsReceived;


    public function __construct()
    {
        $this->roles = ['ROLE_USER'];
        $this->vehicles = new ArrayCollection();
        $this->tripAsDriver = new ArrayCollection();
        $this->updatedAt = new \DateTimeImmutable();
        $this->reviewsGiven = new ArrayCollection();
        $this->reviewsReceived = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getCredits(): int
    {
        return $this->credits;
    }

    public function setCredits(int $credits): static
    {
        $this->credits = $credits;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;
        return $this;
    }

    public function eraseCredentials(): void
    {
    
    }

    public function getProfilePictureFilename(): ?string
    {
        return $this->profilePictureFilename;
    }

    public function setProfilePictureFilename(?string $profilePictureFilename): self
    {
        $this->profilePictureFilename = $profilePictureFilename;
        return $this;
    }

    public function getProfilePictureFile(): ?File
    {
        return $this->profilePictureFile;
    }

    public function setProfilePictureFile(?File $profilePictureFile): self
    {
        $this->profilePictureFile = $profilePictureFile;

        if ($profilePictureFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getUserPreference(): ?UserPreference
    {
        return $this->userPreference;
    }

    public function setUserPreference(?UserPreference $userPreference): static
    {
        if ($userPreference === null && $this->userPreference !== null) {
            $this->userPreference->setUser(null);
        }

        if ($userPreference !== null && $userPreference->getUser() !== $this) {
            $userPreference->setUser($this);
        }

        $this->userPreference = $userPreference;

        return $this;
    }

    public function getVehicles(): Collection
    {
        return $this->vehicles;
    }

    public function addVehicle(Vehicle $vehicle): static
    {
        if (!$this->vehicles->contains($vehicle)) {
            $this->vehicles->add($vehicle);
            $vehicle->setOwner($this);
        }

        return $this;
    }

    public function removeVehicle(Vehicle $vehicle): static
    {
        if ($this->vehicles->removeElement($vehicle)) {
            if ($vehicle->getOwner() === $this) {
                $vehicle->setOwner(null);
            }
        }

        return $this;
    }

    public function isDriver(): bool
    {
        return in_array('ROLE_DRIVER', $this->getRoles());
    }

    public function getDesiredRole(): ?string
    {
        return $this->desiredRole;
    }

    public function setDesiredRole(string $desiredRole): static
    {
        $this->desiredRole = $desiredRole;
        return $this;
    }

    public function getTripAsDriver(): Collection
    {
        return $this->tripAsDriver;
    }

    public function addTripAsDriver(Trip $tripAsDriver): static
    {
        if (!$this->tripAsDriver->contains($tripAsDriver)) {
            $this->tripAsDriver->add($tripAsDriver);
            $tripAsDriver->setDriver($this);
        }

        return $this;
    }

    public function removeTripAsDriver(Trip $tripAsDriver): static
    {
        if ($this->tripAsDriver->removeElement($tripAsDriver)) {
            if ($tripAsDriver->getDriver() === $this) {
                $tripAsDriver->setDriver(null);
            }
        }

        return $this;
    }

    

    public function __sleep()
    {
        return array_diff(array_keys(get_object_vars($this)), ['profilePictureFile']);
    }

    public function getAverageRating(): ?float
    {
        $reviews = $this->getReviewsReceived()->filter(fn(Review $review) => $review->getStatus() === 'approved');

        if ($reviews->isEmpty()) {
            return null;
        }

        $total = 0;
        foreach ($reviews as $review) {
            $total += $review->getRating();
        }

        return $total / $reviews->count();
    }


    public function getReviewsGiven(): Collection
    {
        return $this->reviewsGiven;
    }

    public function addReviewGiven(Review $review): static
    {
        if (!$this->reviewsGiven->contains($review)) {
            $this->reviewsGiven->add($review);
            $review->setUser($this); 
        }

        return $this;
    }

    public function removeReviewGiven(Review $review): static
    {
        if ($this->reviewsGiven->removeElement($review)) {
            if ($review->getUser() === $this) {
                $review->setUser(null);
            }
        }

        return $this;
    }

    public function getReviewsReceived(): Collection
    {
        return $this->reviewsReceived;
    }

    public function addReviewReceived(Review $review): static
    {
        if (!$this->reviewsReceived->contains($review)) {
            $this->reviewsReceived->add($review);
            $review->setRatedDriver($this);
        }

        return $this;
    }

    public function removeReviewReceived(Review $review): static
    {
        if ($this->reviewsReceived->removeElement($review)) {
            if ($review->getRatedDriver() === $this) {
                $review->setRatedDriver(null);
            }
        }

        return $this;
    }
}
