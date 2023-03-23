<?php

namespace App\Controller\User;

use App\Entity\User;
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
    public function __invoke(Request $request, UserManager $userManager)
    {
        $payload = json_decode($request->getContent(), true);
        $user = $userManager->createUser($payload);
        return $user;
    }
}
