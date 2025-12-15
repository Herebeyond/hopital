<?php

namespace App\Entity;

use App\Repository\ProfilRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProfilRepository::class)]
#[ORM\Table(name: 'Role')]
class Profil
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_role', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $role = null;

    #[ORM\ManyToMany(targetEntity: Utilisateur::class, mappedBy: 'profils')]
    private Collection $utilisateurs;

    public function __construct()
    {
        $this->utilisateurs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the role string from the JSON array stored in database
     * Database stores: ["ROLE_ADMIN"], this method returns: ROLE_ADMIN
     */
    public function getRole(): ?string
    {
        if ($this->role === null) {
            return null;
        }
        
        // If it's already a string (not JSON), return it
        if (!str_starts_with($this->role, '[')) {
            return $this->role;
        }
        
        // Decode JSON array and return the first element
        $decoded = json_decode($this->role, true);
        return is_array($decoded) && !empty($decoded) ? $decoded[0] : $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;
        return $this;
    }

    public function getUtilisateurs(): Collection
    {
        return $this->utilisateurs;
    }
}
