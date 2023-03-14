<?php

namespace App\Manager;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Container;

class UserManager extends Manager
{
    private Container $container;

    public function __construct(
    )
    {

    }

    public function createUser(mixed $data): User
    {
        $user = new User();
        $user->setFirstName($data['firstName']);
        $user->setLastName($data['lastName']);
        $user->setEmail($data['email']);
        $user->setPassword($data['password']);
        return $user;

    }
}
