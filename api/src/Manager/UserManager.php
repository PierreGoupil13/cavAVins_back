<?php

namespace App\Manager;

use App\Entity\User;
use App\Repository\UserRepository;
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
    private UserRepository $userRepository;

    public function __construct(
        EntityManagerInterface $em,
        UserPasswordHasherInterface $userPasswordHasher,
        UserRepository $userRepository
    )
    {
        parent::__construct($em);
        $this->userPasswordHasher = $userPasswordHasher;
        $this->userRepository = $userRepository;
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

    public function validateUserOldPassword(array $data)
    {
        if (!isset($data['email']) || !isset($data['oldPassword'])) {
            return null; // Trouver comment retourner une erreur
        }
        $email = $data['email'];
        $password = $data['oldPassword'];
        $user = $this->userRepository->findOneByEmail($email);
        if($user === null){
            return null; // Trouver comment retourner une erreur
        }
        if ($this->userPasswordHasher->isPasswordValid($user,$password)) {
            return $user;
        }
        return null;
    }

    public function changeUserPassword(User $user, array $data) {
        // Check les deux password (existence et validité)
        if (isset($data['newPassword']) && isset($data['newPasswordConfirmation'])){
            if ($data['newPassword'] === $data['newPasswordConfirmation']) {
                // Hash et persiste le nouveau password
                $user->setPlainPassword($data['newPassword']);
                $this->hashPassword($user);
                return $user;
            }
        }
    }
}
