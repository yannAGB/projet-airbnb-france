<?php

namespace App\Entity;

use App\Entity\Enum\BookingStatus;
use App\Repository\BookingRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: BookingRepository::class)]
class Booking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $guest = null;

    #[ORM\ManyToOne(targetEntity: RealEstate::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?RealEstate $realEstate = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $date_arrivee = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $date_depart = null;

    #[ORM\Column]
    private int $nb_nuits = 1;

    #[ORM\Column]
    private int $nb_voyageurs = 1;

    #[ORM\Column]
    private float $montant = 0;

    #[ORM\Column(type: 'string', enumType: BookingStatus::class)]
    private BookingStatus $statut = BookingStatus::EN_ATTENTE;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $note = null;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    public function getId()          : ?int             { return $this->id;           }
    public function getGuest()       : ?User            { return $this->guest;        }
    public function getRealEstate()  : ?RealEstate      { return $this->realEstate;   }
    public function getDateArrivee() : ?\DateTimeImmutable { return $this->date_arrivee; }
    public function getDateDepart()  : ?\DateTimeImmutable { return $this->date_depart;  }
    public function getNbNuits()     : int              { return $this->nb_nuits;     }
    public function getNbVoyageurs() : int              { return $this->nb_voyageurs; }
    public function getMontant()     : float            { return $this->montant;      }
    public function getStatut()      : BookingStatus    { return $this->statut;       }
    public function getNote()        : ?string          { return $this->note;         }
    public function getCreatedAt()   : ?\DateTimeImmutable { return $this->created_at; }
    public function getUpdatedAt()   : ?\DateTimeImmutable { return $this->updated_at; }

    public function setGuest(?User $guest): static         { $this->guest = $guest; return $this; }
    public function setRealEstate(?RealEstate $re): static { $this->realEstate = $re; return $this; }
    public function setDateArrivee(\DateTimeImmutable $d): static { $this->date_arrivee = $d; return $this; }
    public function setDateDepart(\DateTimeImmutable $d): static  { $this->date_depart  = $d; return $this; }
    public function setNbNuits(int $n): static             { $this->nb_nuits = $n; return $this; }
    public function setNbVoyageurs(int $n): static         { $this->nb_voyageurs = $n; return $this; }
    public function setMontant(float $m): static           { $this->montant = $m; return $this; }
    public function setStatut(BookingStatus $s): static    { $this->statut = $s; return $this; }
    public function setNote(?string $n): static            { $this->note = $n; return $this; }
}