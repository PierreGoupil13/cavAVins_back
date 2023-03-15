<?php

namespace App\Manager;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserManager extends Manager
{
    // Le container contient tout les services, mais l'ancienne manière de faire, avant l'injection de services
    /* private Container $container; */

    // ATTENTION, car avec ce constructeur que je laisse vide j'override celui du parent et avec rien, donc si
    // je fait ça il faut au moins appeller le constructeur du parent

    /* public function __construct(
    )
    {

    } */
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(
        EntityManagerInterface $em,
        UserPasswordHasherInterface $userPasswordHasher
    )
    {
        parent::__construct($em);
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function createUser(mixed $data): User
    {
        $user = new User();

        $user->setFirstName($data['firstName']);
        $user->setLastName($data['lastName']);
        $user->setEmail($data['email']);

        // Gère le hashage et persitance du password
        $user->setPlainPassword($data['password']);
        $this->hashPassword($user);

        return $user;
    }

    public function hashPassword(User $user)
    {
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $user->getPlainPassword()));
        $user->eraseCredentials();
    }
}
