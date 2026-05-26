<?php

namespace App\Entity;

use App\Repository\AccessTokenRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccessTokenRepository::class)]
class AccessToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private string $value;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\Column]
    private \DateTimeImmutable $expiresAt;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    public function __construct(User $user, string $value, \DateTimeImmutable $expiresAt)
    {
        $this->user      = $user;
        $this->value     = $value;
        $this->expiresAt = $expiresAt;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId()       : ?int               { return $this->id;        }
    public function getValue()    : string              { return $this->value;     }
    public function getUser()     : User                { return $this->user;      }
    public function getExpiresAt(): \DateTimeImmutable  { return $this->expiresAt; }
    public function getCreatedAt(): \DateTimeImmutable  { return $this->createdAt; }

    public function isValid(): bool
    {
        return $this->expiresAt > new \DateTimeImmutable();
    }
}