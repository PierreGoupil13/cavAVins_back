<?php

namespace App\Manager;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

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

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em);
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
