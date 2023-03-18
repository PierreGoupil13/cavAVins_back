<?php

namespace App\Entity;

use ApiPlatform\Action\PlaceholderAction;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use App\Controller\User\UserChangePasswordController;
use App\Controller\User\UserRegisterController;
use Doctrine\Common\Collections\Collection;
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
            //security: "is_granted('ROLE_ADMIN')", Gestion de rôle ce fait comme cela
            normalizationContext: ['groups' => ['read:Users', 'read:User', 'read:Caves']],
        ),
        new GetCollection(
            uriTemplate: '/users/all',
            //security: "is_granted('ROLE_ADMIN')",
            normalizationContext: ['groups' => ['read:Users']]
        ),
        new Post(
            name: 'registerUser',
            uriTemplate: '/users/register',
            controller: UserRegisterController::class,
            denormalizationContext: ['groups' => ['write:User']],
            openapiContext: [
                'summary' => 'Create a new user',
                'description' => 'Override the user creation to hash password',
                'parameters' => [
                ]
            ]
        ),
        new Put(
            uriTemplate: '/users/unique/{id}/firstName',
            denormalizationContext: ['groups' => ['write:PutFirstName']],
        ),
        new Put(
            uriTemplate: '/users/unique/{id}/lastName',
            denormalizationContext: ['groups' => ['write:PutLastName']],
        ),
        new Put(
            name: 'changePassword',
            uriTemplate: '/users/unique/{id}/changePassword',
            controller: UserChangePasswordController::class,
            openapiContext: [
                'summary' => 'Change password of an existing user',
                'description' => 'Endpoint to change a password',
                'parameters' => [
                ]
            ]
        ),
        new Delete(
            uriTemplate: '/users/unique/{id}',
            security: "is_granted('ROLE_ADMIN')"
        )
    ]
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    // Constantes de clé dans l'api
    const USER_EMAIL_API_KEY = 'email';
    const USER_FIRST_NAME_API_KEY = 'firstName';
    const USER_LAST_NAME_API_KEY = 'lastName';
    const USER_PASSWORD_NAME_API_KEY = 'password';
    const USER_OLD_PASSWORD_NAME_API_KEY = 'oldPassword';
    const USER_NEW_PASSWORD_NAME_API_KEY = 'newPassword';
    const USER_NEW_PASSWORD_CONFIRMATION_NAME_API_KEY = 'newPasswordConfirmation';



    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:Users', 'read:Cave', 'write:User', 'write:PutFirstName'])]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:Users', 'write:User', 'write:PutLastName'])]
    private ?string $lastName = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['read:User', 'write:User'])]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Groups(['write:User'])]
    private ?string $password = null;

    #[Groups(['write:User'])]
    private ?string $plainPassword = null;

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
     * Setter et Getter pour le plain password (intervient à la création d'un User et plus tard la modification)
     */
    public function getPlainPassword(): string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

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
        $this->plainPassword = null;
    }
}
