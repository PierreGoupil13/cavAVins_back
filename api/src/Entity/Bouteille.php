<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\BouteilleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BouteilleRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => ['read:Bouteilles','read:Bouteille']]
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['read:Bouteilles']]
        ),
        new Post()
    ]
)]
class Bouteille
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:Bouteilles'])]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:Bouteille'])]
    private ?string $type = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['read:Bouteille'])]
    private ?\DateTimeInterface $annee = null;

    #[ORM\ManyToMany(targetEntity: Cave::class, mappedBy: 'bouteilles')]
    private Collection $caves;

    public function __construct()
    {
        $this->caves = new ArrayCollection();
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getAnnee(): ?\DateTimeInterface
    {
        return $this->annee;
    }

    public function setAnnee(\DateTimeInterface $annee): self
    {
        $this->annee = $annee;

        return $this;
    }

    /**
     * @return Collection<int, Cave>
     */
    public function getCaves(): Collection
    {
        return $this->caves;
    }

    public function addCafe(Cave $cafe): self
    {
        if (!$this->caves->contains($cafe)) {
            $this->caves->add($cafe);
            $cafe->addBouteille($this);
        }

        return $this;
    }

    public function removeCafe(Cave $cafe): self
    {
        if ($this->caves->removeElement($cafe)) {
            $cafe->removeBouteille($this);
        }

        return $this;
    }
}
