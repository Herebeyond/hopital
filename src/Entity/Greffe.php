<?php

namespace App\Entity;

use App\Repository\GreffeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GreffeRepository::class)]
#[ORM\Table(name: 'Greffe')]
class Greffe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_greffon')]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateGreffe = null;

    #[ORM\Column(nullable: true)]
    private ?int $rangGreffe = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $typeDonneur = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $typeGreffe = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    private ?bool $greffonFonctionnel = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateHeureFin = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $causeFinFonctGreffe = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateDeclampage = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $heureDeclampage = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $cotePrelevementRein = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $coteTransplantationRein = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $ischemieTotal = null;

    #[ORM\Column(nullable: true)]
    private ?int $dureeAnastomoses = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    private ?bool $sondeJj = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $commentaire = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $compteRenduOperatoire = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    private ?bool $protocole = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    private ?bool $dialyse = null;

    #[ORM\OneToOne(inversedBy: 'greffe', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'id_donneur', referencedColumnName: 'id_donneur', nullable: false)]
    private ?Donneur $donneur = null;

    #[ORM\ManyToOne(inversedBy: 'greffes')]
    #[ORM\JoinColumn(name: 'id_patient', referencedColumnName: 'id_patient', nullable: false)]
    private ?Patient $patient = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateGreffe(): ?\DateTimeInterface
    {
        return $this->dateGreffe;
    }

    public function setDateGreffe(?\DateTimeInterface $dateGreffe): static
    {
        $this->dateGreffe = $dateGreffe;
        return $this;
    }

    public function getRangGreffe(): ?int
    {
        return $this->rangGreffe;
    }

    public function setRangGreffe(?int $rangGreffe): static
    {
        $this->rangGreffe = $rangGreffe;
        return $this;
    }

    public function getTypeDonneur(): ?string
    {
        return $this->typeDonneur;
    }

    public function setTypeDonneur(?string $typeDonneur): static
    {
        $this->typeDonneur = $typeDonneur;
        return $this;
    }

    public function getTypeGreffe(): ?string
    {
        return $this->typeGreffe;
    }

    public function setTypeGreffe(?string $typeGreffe): static
    {
        $this->typeGreffe = $typeGreffe;
        return $this;
    }

    public function isGreffonFonctionnel(): ?bool
    {
        return $this->greffonFonctionnel;
    }

    public function setGreffonFonctionnel(?bool $greffonFonctionnel): static
    {
        $this->greffonFonctionnel = $greffonFonctionnel;
        return $this;
    }

    public function getDateHeureFin(): ?\DateTimeInterface
    {
        return $this->dateHeureFin;
    }

    public function setDateHeureFin(?\DateTimeInterface $dateHeureFin): static
    {
        $this->dateHeureFin = $dateHeureFin;
        return $this;
    }

    public function getCauseFinFonctGreffe(): ?string
    {
        return $this->causeFinFonctGreffe;
    }

    public function setCauseFinFonctGreffe(?string $causeFinFonctGreffe): static
    {
        $this->causeFinFonctGreffe = $causeFinFonctGreffe;
        return $this;
    }

    public function getDateDeclampage(): ?\DateTimeInterface
    {
        return $this->dateDeclampage;
    }

    public function setDateDeclampage(?\DateTimeInterface $dateDeclampage): static
    {
        $this->dateDeclampage = $dateDeclampage;
        return $this;
    }

    public function getHeureDeclampage(): ?\DateTimeInterface
    {
        return $this->heureDeclampage;
    }

    public function setHeureDeclampage(?\DateTimeInterface $heureDeclampage): static
    {
        $this->heureDeclampage = $heureDeclampage;
        return $this;
    }

    public function getCotePrelevementRein(): ?string
    {
        return $this->cotePrelevementRein;
    }

    public function setCotePrelevementRein(?string $cotePrelevementRein): static
    {
        $this->cotePrelevementRein = $cotePrelevementRein;
        return $this;
    }

    public function getCoteTransplantationRein(): ?string
    {
        return $this->coteTransplantationRein;
    }

    public function setCoteTransplantationRein(?string $coteTransplantationRein): static
    {
        $this->coteTransplantationRein = $coteTransplantationRein;
        return $this;
    }

    public function getIschemieTotal(): ?\DateTimeInterface
    {
        return $this->ischemieTotal;
    }

    public function setIschemieTotal(?\DateTimeInterface $ischemieTotal): static
    {
        $this->ischemieTotal = $ischemieTotal;
        return $this;
    }

    public function getDureeAnastomoses(): ?int
    {
        return $this->dureeAnastomoses;
    }

    public function setDureeAnastomoses(?int $dureeAnastomoses): static
    {
        $this->dureeAnastomoses = $dureeAnastomoses;
        return $this;
    }

    public function isSondeJj(): ?bool
    {
        return $this->sondeJj;
    }

    public function setSondeJj(?bool $sondeJj): static
    {
        $this->sondeJj = $sondeJj;
        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): static
    {
        $this->commentaire = $commentaire;
        return $this;
    }

    public function getCompteRenduOperatoire(): ?string
    {
        return $this->compteRenduOperatoire;
    }

    public function setCompteRenduOperatoire(?string $compteRenduOperatoire): static
    {
        $this->compteRenduOperatoire = $compteRenduOperatoire;
        return $this;
    }

    public function isProtocole(): ?bool
    {
        return $this->protocole;
    }

    public function setProtocole(?bool $protocole): static
    {
        $this->protocole = $protocole;
        return $this;
    }

    public function isDialyse(): ?bool
    {
        return $this->dialyse;
    }

    public function setDialyse(?bool $dialyse): static
    {
        $this->dialyse = $dialyse;
        return $this;
    }

    public function getDonneur(): ?Donneur
    {
        return $this->donneur;
    }

    public function setDonneur(?Donneur $donneur): static
    {
        $this->donneur = $donneur;
        return $this;
    }

    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    public function setPatient(?Patient $patient): static
    {
        $this->patient = $patient;
        return $this;
    }
}
