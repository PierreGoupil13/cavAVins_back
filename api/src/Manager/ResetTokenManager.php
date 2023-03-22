<?php

namespace App\Manager;

use App\Entity\ResetToken;
use App\Entity\User;
use App\Repository\ResetTokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class ResetTokenManager extends Manager
{
    private ResetTokenRepository $resetTokenRepository;
    private JWTTokenManagerInterface $jwtTokenManager;

    public function __construct(
        EntityManagerInterface $em,
        ResetTokenRepository $resetTokenRepository,
        JWTTokenManagerInterface $jwtTokenManager
    )
    {
        parent::__construct($em);
        $this->resetTokenRepository = $resetTokenRepository;
        $this->jwtTokenManager = $jwtTokenManager;
    }

    public function resetTokenExist(User $user): bool
    {
        // Verifier si un token exist pour un UserId donnée
        return([] !== ($this->resetTokenRepository->findResetToken($user->getId())));
    }

    public function createResetToken(User $user)
    {
        $resetToken = new ResetToken();
        $resetToken->setBearer($user);
        $token = $this->jwtTokenManager->create($user);
        $resetToken->setJwtToken($token);
        return $resetToken;
    }

    public function resetTokenValid(string $tokenPayload, User $user)
    {
        $tokenUser = $this->resetTokenRepository->findResetToken($user->getId())[0]->getJwtToken();
        return($tokenUser === $tokenPayload);
    }

    public function getResetToken(User $user)
    {
        $tokenUser = $this->resetTokenRepository->findResetToken($user->getId())[0];
        return($tokenUser);
    }

    public function removeResetToken(ResetToken $token)
    {
        $this->resetTokenRepository->remove($token);
    }
}
