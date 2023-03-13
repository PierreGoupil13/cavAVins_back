<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Composer\XdebugHandler\Status;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\Common\Collections\Collection;
use App\Controller\User\UserCountController;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/users/unique/{id}',
            normalizationContext: ['groups' => ['read:Users', 'read:User', 'read:Caves']],
        ),
        new GetCollection(
            uriTemplate: '/users/all',
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
        ),
        new Put(
            uriTemplate: '/users/unique/{id}'
        ),
        new Delete(
            uriTemplate: '/users/unique/{id}'
        )
    ]
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
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

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['read:User', 'write:User'])]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Groups(['write:User'])]
    private ?string $password = null;

    // Ajouter car necessaire pour implementer les interfaces
    #[ORM\Column(type: 'json')]
    private ?array $roles = [];

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
            $cave->setOwner($this);
        }

        return $this;
    }

    public function removeCave(Cave $cave): self
    {
        if ($this->caves->removeElement($cave)) {
            // set the owning side to null (unless already changed)
            if ($cave->getOwner() === $this) {
                $cave->setOwner(null);
            }
        }

        return $this;
    }

    // Elements relatif a la securite
    /**
     * The public representation of the user (e.g. a username, an email address, etc.)
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->getEmail();
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
