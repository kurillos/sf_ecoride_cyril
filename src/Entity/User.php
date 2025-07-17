<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToOne;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use App\Entity\Vehicle;
use Doctrine\DBAL\Types\Types;

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

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
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

    #[ORM\Column(length: 255, unique: true)] // Généralement, le pseudo est unique
    #[Assert\NotBlank(message: 'Le pseudonyme ne peut pas être vide.')]
    #[Assert\Length(min: 3, max: 255, minMessage: 'Le pseudonyme doit contenir au moins {{ limit }} caractères.')]
    private ?string $pseudo = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * #[Vich\UploadableField(mapping: 'profile_picture', fileNameProperty: 'profilePictureFilename')]
     * @var File|null
     */
    #[Vich\UploadableField(mapping: 'profile_picture', fileNameProperty: 'profilePictureFilename')]
    private ?File $profilePictureFile = null;

    /**
     * @ORM\Column(type: 'string', nullable: true)
     * @var string|null
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $profilePictureFilename = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?UserPreference $userPreference = null;

    #[ORM\OneToMany(targetEntity: Vehicle::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $vehicles;

    #[ORM\Column(length: 255, options: ['default' => 'passenger'])]
    private ?string $desiredRole = 'passenger';

    public function __construct()
    {
        $this->vehicles = new ArrayCollection();
        $this->updatedAt = new \DateTimeImmutable();
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

    /**
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
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

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // $this->plainPassword = null;
    }

    public function getProfilePictureFile(): ?File
    {
        return $this->profilePictureFile;
    }

    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setProfilePictureFile(?File $profilePictureFile = null): void
    {
        $this->profilePictureFile = $profilePictureFile;

        if (null !== $profilePictureFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
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
            $vehicle->setUser($this);
        }

        return $this;
    }

    public function removeVehicle(Vehicle $vehicle): static
    {
        if ($this->vehicles->removeElement($vehicle)){
            if ($vehicle->getUser() === $this) {
                $vehicle->setUser(null);
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
}
