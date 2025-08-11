<?php

namespace App\Entity;

use App\Repository\ReportRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReportRepository::class)]
class Report
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le motif du signalement ne peut pas être vide.')]
    private ?string $reason = null;

    #[ORM\Column(length: 50)]
    private ?string $status = 'pending';

    #[ORM\ManyToOne(inversedBy: 'sentReports')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $reporter = null;

    #[ORM\ManyToOne(inversedBy: 'reports')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $reportedTrip = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(string $reason): static
    {
        $this->reason = $reason;

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

    public function getReporter(): ?User
    {
        return $this->reporter;
    }

    public function setReporter(?User $reporter): static
    {
        $this->reporter = $reporter;

        return $this;
    }


    public function getReportedTrip(): ?Trip
    {
        return $this->reportedTrip;
    }

    public function setreportedTrip(?Trip $reportedTrip): static
    {
        $this->reportedTrip = $reportedTrip;

        return $this;
    }

}