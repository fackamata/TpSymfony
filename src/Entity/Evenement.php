<?php

namespace App\Entity;

use App\Repository\EvenementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EvenementRepository::class)
 */
class Evenement
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
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $prixEntree;

    /**
     * @ORM\ManyToOne(targetEntity=Lieu::class, inversedBy="evenements")
     * @ORM\JoinColumn(nullable=false)
     */
    private $lieu;

    /**
     * @ORM\ManyToMany(targetEntity=Oeuvre::class, inversedBy="evenements")
     */
    private $oeuvre;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="evenements")
     */
    private $user;

    public function __construct()
    {
        $this->oeuvre = new ArrayCollection();
        $this->user = new ArrayCollection();
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

    public function __toString(): string
    {
        return $this->getNom();
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getPrixEntree(): ?float
    {
        return $this->prixEntree;
    }

    public function setPrixEntree(?float $prixEntree): self
    {
        $this->prixEntree = $prixEntree;

        return $this;
    }

    public function getLieu(): ?Lieu
    {
        return $this->lieu;
    }

    public function setLieu(?Lieu $lieu): self
    {
        $this->lieu = $lieu;

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
        }

        return $this;
    }

    public function removeOeuvre(Oeuvre $oeuvre): self
    {
        $this->oeuvre->removeElement($oeuvre);

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(User $user): self
    {
        if (!$this->user->contains($user)) {
            $this->user[] = $user;
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        $this->user->removeElement($user);

        return $this;
    }
}
