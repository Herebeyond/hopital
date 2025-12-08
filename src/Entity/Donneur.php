<?php

namespace App\Entity;

use App\Repository\DonneurRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DonneurRepository::class)]
#[ORM\Table(name: 'Donneur')]
class Donneur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_donneur', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $nCristal = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $gSanguin = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    private ?bool $sexe = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $age = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $poids = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $commentairePatient = null;

    #[ORM\OneToOne(mappedBy: 'donneur', cascade: ['persist', 'remove'])]
    private ?Greffe $greffe = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNCristal(): ?string
    {
        return $this->nCristal;
    }

    public function setNCristal(?string $nCristal): static
    {
        $this->nCristal = $nCristal;
        return $this;
    }

    public function getGSanguin(): ?string
    {
        return $this->gSanguin;
    }

    public function setGSanguin(?string $gSanguin): static
    {
        $this->gSanguin = $gSanguin;
        return $this;
    }

    public function getSexe(): ?bool
    {
        return $this->sexe;
    }

    public function setSexe(?bool $sexe): static
    {
        $this->sexe = $sexe;
        return $this;
    }

    public function getAge(): ?\DateTimeInterface
    {
        return $this->age;
    }

    public function setAge(?\DateTimeInterface $age): static
    {
        $this->age = $age;
        return $this;
    }

    public function getPoids(): ?string
    {
        return $this->poids;
    }

    public function setPoids(?string $poids): static
    {
        $this->poids = $poids;
        return $this;
    }

    public function getCommentairePatient(): ?string
    {
        return $this->commentairePatient;
    }

    public function setCommentairePatient(?string $commentairePatient): static
    {
        $this->commentairePatient = $commentairePatient;
        return $this;
    }

    public function getGreffe(): ?Greffe
    {
        return $this->greffe;
    }

    public function setGreffe(?Greffe $greffe): static
    {
        if ($greffe === null && $this->greffe !== null) {
            $this->greffe->setDonneur(null);
        }

        if ($greffe !== null && $greffe->getDonneur() !== $this) {
            $greffe->setDonneur($this);
        }

        $this->greffe = $greffe;
        return $this;
    }
}
