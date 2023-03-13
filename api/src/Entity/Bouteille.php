<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
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
            uriTemplate: '/bouteilles/unique/{id}',
            normalizationContext: ['groups' => ['read:Bouteilles','read:Bouteille']]
        ),
        new GetCollection(
            uriTemplate: '/bouteilles/all',
            normalizationContext: ['groups' => ['read:Bouteilles']]
        ),
        new Post(
            uriTemplate: '/bouteilles',
            denormalizationContext: ['groups' => ['write:Bouteille']]
        ),
        new Put(
            uriTemplate: '/bouteilles/unique/{id}'
        ),
        new Delete(
            uriTemplate: '/bouteilles/unique/{id}'
        )
        ]
)]
class Bouteille
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:Bouteilles','write:Bouteille'])]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:Bouteille','write:Bouteille'])]
    private ?string $type = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['read:Bouteille','write:Bouteille'])]
    private ?\DateTimeInterface $annee = null;

    #[ORM\ManyToMany(targetEntity: Cave::class, mappedBy: 'bouteilles')]
    #[Groups(['read:Bouteille'])]
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

    public function addCave(Cave $cave): self
    {
        if (!$this->caves->contains($cave)) {
            $this->caves->add($cave);
            $cave->addBouteille($this);
        }

        return $this;
    }

    public function removeCave(Cave $cave): self
    {
        if ($this->caves->removeElement($cave)) {
            $cave->removeBouteille($this);
        }

        return $this;
    }
}
