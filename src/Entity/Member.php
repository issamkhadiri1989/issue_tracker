<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\MemberRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: MemberRepository::class)]
#[ORM\Table(name: '`member`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class Member implements UserInterface, PasswordAuthenticatedUserInterface
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

    #[ORM\Column(length: 255)]
    private ?string $fullName = null;

    /**
     * @var Collection<int, Issue>
     */
    #[ORM\OneToMany(targetEntity: Issue::class, mappedBy: 'reporter')]
    private Collection $issues;

    /**
     * @var Collection<int, Issue>
     */
    #[ORM\OneToMany(targetEntity: Issue::class, mappedBy: 'assingee')]
    private Collection $attachedIssues;

    public function __construct()
    {
        $this->issues = new ArrayCollection();
        $this->attachedIssues = new ArrayCollection();
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
     * A visual identifier that represents this user.
     *
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
        // guarantee every user at least has ROLE_USER
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

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): static
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * @return Collection<int, Issue>
     */
    public function getIssues(): Collection
    {
        return $this->issues;
    }

    public function addIssue(Issue $issue): static
    {
        if (!$this->issues->contains($issue)) {
            $this->issues->add($issue);
            $issue->setReporter($this);
        }

        return $this;
    }

    public function removeIssue(Issue $issue): static
    {
        if ($this->issues->removeElement($issue)) {
            // set the owning side to null (unless already changed)
            if ($issue->getReporter() === $this) {
                $issue->setReporter(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Issue>
     */
    public function getAttachedIssues(): Collection
    {
        return $this->attachedIssues;
    }

    public function addAttachedIssue(Issue $attachedIssue): static
    {
        if (!$this->attachedIssues->contains($attachedIssue)) {
            $this->attachedIssues->add($attachedIssue);
            $attachedIssue->setAssingee($this);
        }

        return $this;
    }

    public function removeAttachedIssue(Issue $attachedIssue): static
    {
        if ($this->attachedIssues->removeElement($attachedIssue)) {
            // set the owning side to null (unless already changed)
            if ($attachedIssue->getAssingee() === $this) {
                $attachedIssue->setAssingee(null);
            }
        }

        return $this;
    }
}
