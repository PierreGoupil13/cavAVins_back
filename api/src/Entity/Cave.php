<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CaveRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CaveRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/caves/unique/{id}',
            normalizationContext : ['groups' => ['read:Caves','read:Cave']]
        ),
        new GetCollection(
            uriTemplate: '/caves/all',
            normalizationContext: ['groups' => ['read:Caves']]
        ),
        new Post(
            denormalizationContext: ['groups' => ['write:Cave']]
        ),
        new Put(
            uriTemplate: '/caves/unique/{id}',
        ),
        new Delete(
            uriTemplate: '/caves/unique/{id}',
        )

        ]
)]
class Cave
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:Caves','write:Cave'])]
    private ?string $nom = null;


    #[Groups(['read:Cave','write:Cave'])]
    #[ORM\ManyToOne(inversedBy: 'caves')]
    private ?User $owner = null;

    #[Groups(['read:Cave'])]
    #[ORM\ManyToMany(targetEntity: Bouteille::class, inversedBy: 'caves')]
    private Collection $bouteilles;

    public function __construct()
    {
        $this->bouteilles = new ArrayCollection();
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

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, Bouteille>
     */
    public function getBouteilles(): Collection
    {
        return $this->bouteilles;
    }

    public function addBouteille(Bouteille $bouteille): self
    {
        if (!$this->bouteilles->contains($bouteille)) {
            $this->bouteilles->add($bouteille);
        }

        return $this;
    }

    public function removeBouteille(Bouteille $bouteille): self
    {
        $this->bouteilles->removeElement($bouteille);

        return $this;
    }
}
