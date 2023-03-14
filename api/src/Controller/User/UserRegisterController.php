<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Manager\PayloadManager;
use App\Manager\UserManager;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class UserRegisterController
{
    /* public function __construct(UserRepository $userRepository)
    {

    } */
    public function __invoke(Request $request, UserManager $userManager, PayloadManager $payloadManager)
    {
        // Decode la payload
        $payload = json_decode($request->getContent(), true);

        // Extrait les infos de la payload
        $data = $payloadManager->extractUserPayload($payload);
        dd($data);
        // Crée un nouveau User
        $user = $userManager->createUser($data);

        // Return et persist
        return $user;
    }
}
