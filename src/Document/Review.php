<?php

namespace App\Document;

use App\Repository\ReviewRepository;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

#[ODM\Document(repositoryClass: ReviewRepository::class)]
class Review
{
    #[ODM\Id]
    protected $id;

    #[ODM\Field(type: 'string')]
    private string $comment;

    #[ODM\Field(type: 'int')]
    private int $rating;

    #[ODM\Field(type: 'string')]
    private string $ratedDriverId;

    #[ODM\Field(type: 'string')]
    private string $status = 'pending';

    #[ODM\Field(type: 'date_immutable', nullable: true)]
    private ?\DateTimeImmutable $validatedAt = null;

    #[ODM\Field(type: 'date_immutable', nullable: true)]
    private ?\DateTimeImmutable $rejectedAt = null;

    #[ODM\Field(type: 'string')]
    private ?string $userId = null;

    #[ODM\Field(type: 'date_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    public function getId(): ?string
    {
        return $this->id;
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

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(?int $rating): static
    {
        $this->rating = $rating;

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

    public function getRatedDriverId(): ?string
    {
        return $this->ratedDriverId;
    }

    public function setRatedDriverId(?string $ratedDriverId): static
    {
        $this->ratedDriverId = $ratedDriverId;

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

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function setUserId(?string $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getValidatedAt(): ?\DateTimeImmutable
    {
        return $this->validatedAt;
    }

    public function setValidatedAt(?\DateTimeImmutable $validateAt): static
    {
        $this->validatedAt = $validateAt;

        return $this;
    }

    public function getRejectedAt(): ?\DateTimeImmutable
    {
        return $this->rejectedAt;
    }

    public function setRejectedAt(?\DateTimeImmutable $rejectedAt): static
    {
        $this->rejectedAt = $rejectedAt;

        return $this;
    }
}

