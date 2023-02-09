<?php

namespace App\Controller\User;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class UserCountController extends AbstractController
{
    public function __construct(private UserRepository $userRepository)
    {

    }

    public function __invoke(): int
    {
        return $this->userRepository->count([]);
    }

}
