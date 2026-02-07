<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\IssueStatusEnum;
use App\Enum\SeverityEnum;
use App\Repository\IssueRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: IssueRepository::class)]
#[ORM\HasLifecycleCallbacks()]
class Issue
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\Length(max: 100), Assert\NotBlank]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTime $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $updatedAt = null;

    #[ORM\Column(enumType: SeverityEnum::class, options: ['default' => SeverityEnum::LOW])]
    private ?SeverityEnum $severity = null;

    #[ORM\ManyToOne(inversedBy: 'issues')]
    private ?Category $category = null;

    #[ORM\ManyToOne(inversedBy: 'issues')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Member $reporter = null;

    #[ORM\ManyToOne(inversedBy: 'attachedIssues')]
    private ?Member $assignee = null;

    #[ORM\Column(enumType: IssueStatusEnum::class, options: ['default' => IssueStatusEnum::BACKLOG])]
    private ?IssueStatusEnum $status = null;

    private string $currentStatus;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    #[ORM\PrePersist()]
    public function assignCreationDate(): void
    {
        $this->setCreatedAt(new \DateTime());
    }

    #[ORM\PreUpdate()]
    public function assignUpdateDate(): void
    {
        $this->setUpdatedAt(new \DateTime());
    }

    public function getSeverity(): ?SeverityEnum
    {
        return $this->severity;
    }

    public function setSeverity(SeverityEnum $severity): static
    {
        $this->severity = $severity;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getReporter(): ?Member
    {
        return $this->reporter;
    }

    public function setReporter(?Member $reporter): static
    {
        $this->reporter = $reporter;

        return $this;
    }

    public function getAssignee(): ?Member
    {
        return $this->assignee;
    }

    public function setAssignee(?Member $assignee): static
    {
        $this->assignee = $assignee;

        return $this;
    }

    public function getStatus(): ?IssueStatusEnum
    {
        return $this->status;
    }

    public function setStatus(IssueStatusEnum $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCurrentStatus(): string
    {
        return $this->currentStatus;
    }

    public function setCurrentStatus(string $currentStatus): void
    {
        $this->currentStatus = $currentStatus;
    }

    public function canBeChanged(): bool
    {
        return IssueStatusEnum::CLOSED !== $this->getStatus() && IssueStatusEnum::DONE !== $this->getStatus();
    }
}
