<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Controller\User\UserCountController;
use App\Repository\UserRepository;
use Composer\XdebugHandler\Status;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => ['read:Users', 'read:User', 'read:Caves']],
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['read:Users']]
        ),
        new Post(
            denormalizationContext: ['groups' => ['write:User']]
        ),
        new GetCollection(
            name: 'count',
            uriTemplate: '/users/all/count',
            controller: UserCountController::class,
            openapiContext: [
                'summary' => 'Retrive the total number of users',
                'description' => 'Retrive the total number of users',
                'parameters' => []
            ]
        )
    ]
)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:Users', 'read:Cave', 'write:User'])]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:Users', 'write:User'])]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:User', 'write:User'])]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Groups(['write:User'])]
    private ?string $pwd = null;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Cave::class)]
    #[Groups(['read:User'])]
    private Collection $caves;

    public function __construct()
    {
        $this->caves = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

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

    public function getPwd(): ?string
    {
        return $this->pwd;
    }

    public function setPwd(string $pwd): self
    {
        $this->pwd = $pwd;

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
            $cafe->setOwner($this);
        }

        return $this;
    }

    public function removeCafe(Cave $cafe): self
    {
        if ($this->caves->removeElement($cafe)) {
            // set the owning side to null (unless already changed)
            if ($cafe->getOwner() === $this) {
                $cafe->setOwner(null);
            }
        }

        return $this;
    }
}
