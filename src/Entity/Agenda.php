<?php

namespace App\Entity;

use App\Entity\Enum\AgendaStatus;
use App\Repository\AgendaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AgendaRepository::class)]
class Agenda
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $date_arrivee = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $date_depart = null;

    #[ORM\Column(length: 150)]
    private ?string $note = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'agenda')]
    private Collection $userAgenda;

    /**
     * @var Collection<int, RealEstate>
     */
    #[ORM\OneToMany(targetEntity: RealEstate::class, mappedBy: 'agenda')]
    private Collection $realEstate;

	#[ORM\Column(type: 'string', enumType: AgendaStatus::class)]
	private ?AgendaStatus $status = AgendaStatus::PENDING;

    public function __construct()
    {
        $this->userAgenda = new ArrayCollection();
        $this->realEstate = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateArrivee(): ?\DateTimeImmutable
    {
        return $this->date_arrivee;
    }

    public function setDateArrivee(\DateTimeImmutable $date_arrivee): static
    {
        $this->date_arrivee = $date_arrivee;

        return $this;
    }

    public function getDateDepart(): ?\DateTimeImmutable
    {
        return $this->date_depart;
    }

    public function setDateDepart(\DateTimeImmutable $date_depart): static
    {
        $this->date_depart = $date_depart;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): static
    {
        $this->note = $note;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUserAgenda(): Collection
    {
        return $this->userAgenda;
    }

    public function addUserAgenda(User $userAgenda): static
    {
        if (!$this->userAgenda->contains($userAgenda)) {
            $this->userAgenda->add($userAgenda);
            $userAgenda->setAgenda($this);
        }

        return $this;
    }

    public function removeUserAgenda(User $userAgenda): static
    {
        if ($this->userAgenda->removeElement($userAgenda)) {
            // set the owning side to null (unless already changed)
            if ($userAgenda->getAgenda() === $this) {
                $userAgenda->setAgenda(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, RealEstate>
     */
    public function getRealEstate(): Collection
    {
        return $this->realEstate;
    }

    public function addRealEstate(RealEstate $realEstate): static
    {
        if (!$this->realEstate->contains($realEstate)) {
            $this->realEstate->add($realEstate);
            $realEstate->setAgenda($this);
        }

        return $this;
    }

    public function removeRealEstate(RealEstate $realEstate): static
    {
        if ($this->realEstate->removeElement($realEstate)) {
            // set the owning side to null (unless already changed)
            if ($realEstate->getAgenda() === $this) {
                $realEstate->setAgenda(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?AgendaStatus
    {
        return $this->status;
    }

    public function setStatus(?AgendaStatus $status): static
    {
        $this->status = $status;

        return $this;
    }
}
