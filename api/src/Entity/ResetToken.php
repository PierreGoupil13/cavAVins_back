<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ResetTokenRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ResetTokenRepository::class)]
#[ApiResource(
    operations: [
    ]
)]
class ResetToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(User::READ_PASSWORD)]
    private ?string $jwtToken = null;

    #[ORM\OneToOne(inversedBy: 'resetToken')] //cascade: ['persist', 'remove'])
    #[ORM\JoinColumn(nullable: false)]
    private ?User $bearer = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getJwtToken(): ?string
    {
        return $this->jwtToken;
    }

    public function setJwtToken(?string $jwtToken): self
    {
        $this->jwtToken = $jwtToken;

        return $this;
    }

    public function getBearer(): ?User
    {
        return $this->bearer;
    }

    public function setBearer(User $bearer): self
    {
        $this->bearer = $bearer;

        return $this;
    }
}
