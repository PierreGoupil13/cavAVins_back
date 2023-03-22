<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Controller\User\UserChangePasswordController;
use App\Controller\User\UserChangePwdWithTokenController;
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
            normalizationContext: ['groups' => [User::READ_USER]],
        ),
        new Put(
            name:'ChangePwdToken',
            uriTemplate:'/users/changePwdToken',
            denormalizationContext: ['groups' => [User::PUT_PASSWORD]],
            normalizationContext: ['groups' => [User::READ_PASSWORD]],
            controller: UserChangePwdWithTokenController::class
        ),
        new GetCollection(
            uriTemplate: '/users/all',
            //security: "is_granted('ROLE_ADMIN')",
            normalizationContext: ['groups' => [User::READ_USER]]
        ),
        new Post(
            name: 'registerUser',
            uriTemplate: '/users/register',
            controller: UserRegisterController::class,
            denormalizationContext: ['groups' => [User::REGISTER_USER]],
            normalizationContext: ['groups' => [User::READ_USER]],
            openapiContext: [
                'summary' => 'Create a new user',
                'description' => 'Create a new user with a hashed password'
            ]
        ),
        new Put(
            uriTemplate: '/users/unique/{id}/firstName',
            denormalizationContext: ['groups' => [User::PUT_FIRST_NAME]],
            normalizationContext: ['groups' => [User::READ_FIRST_NAME]],
        ),
        new Put(
            uriTemplate: '/users/unique/{id}/lastName',
            denormalizationContext: ['groups' => [User::PUT_LAST_NAME]],
            normalizationContext: ['groups' => [User::READ_LAST_NAME]],
        ),
        new Put(
            name: 'changePassword',
            uriTemplate: '/users/unique/{id}/changePassword',
            controller: UserChangePasswordController::class,
            denormalizationContext: ['groups' => [User::PUT_PASSWORD]],
            normalizationContext: ['groups'=> [User::READ_PASSWORD]],
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
    const USER_RESET_TOKEN_API_KEY = 'resetJwt';

    // Constantes de serialisation
    const READ_USER = 'read:Users';
    const REGISTER_USER = 'read:registerUser';
    const PUT_FIRST_NAME = 'write:putLastName';
    const PUT_LAST_NAME = 'write:putFirstName';
    const PUT_PASSWORD = 'write:putPassword';
    const READ_PASSWORD = 'read:onlyPassword';
    const READ_FIRST_NAME = 'read:onlyFirstName';
    const READ_LAST_NAME = 'read:onlyLastName';


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([User::READ_USER])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups([
        User::READ_USER,
        User::REGISTER_USER,
        User::READ_FIRST_NAME,
        User::PUT_FIRST_NAME
        ])]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[Groups([
        User::READ_USER,
        User::REGISTER_USER,
        User::READ_LAST_NAME,
        User::PUT_LAST_NAME
        ])]
    private ?string $lastName = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups([
        User::READ_USER,
        User::REGISTER_USER
        ])]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Groups([
        User::REGISTER_USER,
        User::PUT_PASSWORD,
        User::READ_PASSWORD
        ])]
    private ?string $password = null;

    private ?string $plainPassword = null;

    // Ajouter car necessaire pour implementer les interfaces
    #[ORM\Column(type: 'json')]
    private ?array $roles = [];

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Cave::class)]
    //#[Groups()]
    private Collection $caves;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $jwt = null;

    #[ORM\OneToOne(mappedBy: 'bearer', cascade: ['persist', 'remove'])]
    private ?ResetToken $resetToken = null;



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

    public function getJwt(): ?string
    {
        return $this->jwt;
    }

    /**
     * [Description for setJwt]
     *
     * @param string|null $jwt
     *
     * @return self
     *
     */
    public function setJwt(?string $jwt): self
    {
        $this->jwt = $jwt;

        return $this;
    }

    public function getResetToken(): ?ResetToken
    {
        return $this->resetToken;
    }

    public function setResetToken(ResetToken $resetToken): self
    {
        // set the owning side of the relation if necessary
        if ($resetToken->getBearer() !== $this) {
            $resetToken->setBearer($this);
        }

        $this->resetToken = $resetToken;

        return $this;
    }
}
