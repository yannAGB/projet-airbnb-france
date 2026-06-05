<?php

namespace App\Entity;

use App\Repository\LikeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LikeRepository::class)]
#[ORM\Table(name: '`like`')]
class Like
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $review = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

	#[ORM\ManyToOne(targetEntity: RealEstate::class, inversedBy: 'reviews')]
	#[ORM\JoinColumn(nullable: true)]
	private ?RealEstate $realEstate = null;

	#[ORM\ManyToOne(targetEntity: User::class)]
	#[ORM\JoinColumn(nullable: true)]
	private ?User $reviewer = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReview(): ?int
    {
        return $this->review;
    }

    public function setReview(int $review): static
    {
        $this->review = $review;

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
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

	public function getRealEstate(): ?RealEstate { return $this->realEstate; }
	public function setRealEstate(?RealEstate $re): static
	{
		$this->realEstate = $re;
		return $this;
	}

	public function getReviewer(): ?User { return $this->reviewer; }
	public function setReviewer(?User $u): static
	{
		$this->reviewer = $u;
		return $this;
	}
}
