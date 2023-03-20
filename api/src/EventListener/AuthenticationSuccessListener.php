<?php

namespace App\EventListener;

use App\Manager\UserManager;
use Doctrine\ORM\EntityManager;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

class AuthenticationSuccessListener
{
    private UserManager $userManager;

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    public function onAuthenticationSuccessResponse (AuthenticationSuccessEvent $event)
    {
        // Intercepte l'évenement d'authentication réussie et récupère le User et le Token
        $token = $event->getData()['token'];
        $user = $event->getUser();
        // Persist le token en base pour cet utilisateur
        $this->userManager->updateJwtToken($user, $token);
    }
}
