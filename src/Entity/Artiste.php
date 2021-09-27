<?php

namespace App\Entity;

use App\Repository\ArtisteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ArtisteRepository::class)
 */
class Artiste
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity=Oeuvre::class, mappedBy="artiste")
     */
    private $oeuvre;

    public function __construct()
    {
        $this->oeuvre = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection|Oeuvre[]
     */
    public function getOeuvre(): Collection
    {
        return $this->oeuvre;
    }

    public function addOeuvre(Oeuvre $oeuvre): self
    {
        if (!$this->oeuvre->contains($oeuvre)) {
            $this->oeuvre[] = $oeuvre;
            $oeuvre->setArtiste($this);
        }

        return $this;
    }

    public function removeOeuvre(Oeuvre $oeuvre): self
    {
        if ($this->oeuvre->removeElement($oeuvre)) {
            // set the owning side to null (unless already changed)
            if ($oeuvre->getArtiste() === $this) {
                $oeuvre->setArtiste(null);
            }
        }

        return $this;
    }
}
