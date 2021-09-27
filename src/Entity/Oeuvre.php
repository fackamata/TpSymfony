<?php

namespace App\Entity;

use App\Repository\OeuvreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OeuvreRepository::class)
 */
class Oeuvre
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
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateCreation;

    /**
     * @ORM\Column(type="integer")
     */
    private $prix;

    /**
     * @ORM\ManyToMany(targetEntity=Evenement::class, mappedBy="oeuvre")
     */
    private $evenements;

    /**
     * @ORM\ManyToOne(targetEntity=Type::class, inversedBy="oeuvres")
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity=Artiste::class, inversedBy="oeuvre")
     */
    private $artiste;

    /**
     * @ORM\OneToMany(targetEntity=Enchere::class, mappedBy="oeuvre")
     */
    private $enchere;

    public function __construct()
    {
        $this->evenements = new ArrayCollection();
        $this->enchere = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(int $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * @return Collection|Evenement[]
     */
    public function getEvenements(): Collection
    {
        return $this->evenements;
    }

    public function addEvenement(Evenement $evenement): self
    {
        if (!$this->evenements->contains($evenement)) {
            $this->evenements[] = $evenement;
            $evenement->addOeuvre($this);
        }

        return $this;
    }

    public function removeEvenement(Evenement $evenement): self
    {
        if ($this->evenements->removeElement($evenement)) {
            $evenement->removeOeuvre($this);
        }

        return $this;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getArtiste(): ?Artiste
    {
        return $this->artiste;
    }

    public function setArtiste(?Artiste $artiste): self
    {
        $this->artiste = $artiste;

        return $this;
    }

    /**
     * @return Collection|Enchere[]
     */
    public function getEnchere(): Collection
    {
        return $this->enchere;
    }

    public function addEnchere(Enchere $enchere): self
    {
        if (!$this->enchere->contains($enchere)) {
            $this->enchere[] = $enchere;
            $enchere->setOeuvre($this);
        }

        return $this;
    }

    public function removeEnchere(Enchere $enchere): self
    {
        if ($this->enchere->removeElement($enchere)) {
            // set the owning side to null (unless already changed)
            if ($enchere->getOeuvre() === $this) {
                $enchere->setOeuvre(null);
            }
        }

        return $this;
    }
}
