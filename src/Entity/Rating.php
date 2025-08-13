<?php

namespace App\Entity;

use App\Repository\RatingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RatingRepository::class)]
class Rating
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $ratedUser = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $ratingUser = null;

    #[ORM\Column]
    private ?int $rating = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $comment = null;

    #[ORM\ManyToOne(inversedBy: 'ratings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Trip $trip = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRatedUser(): ?User
    {
        return $this->ratedUser;
    }

    public function setRatedUser(?User $ratedUser): static
    {
        $this->ratedUser = $ratedUser;

        return $this;
    }

    public function getRatingUser(): ?User
    {
        return $this->ratingUser;
    }

    public function setratingUser(?User $ratingUser): static
    {
        $this->ratingUser = $ratingUser;

        return $this;
    }

    public function getrating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): static
    {
        $this->rating = $rating;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

     public function getTrip(): ?Trip
    {
        return $this->trip;
    }
    
    public function setTrip(?Trip $trip): static
    {
        $this->trip = $trip;
        
        return $this;
    }
}
