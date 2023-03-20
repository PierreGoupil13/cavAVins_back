<?php

namespace App\Manager;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Mime\RawMessage;

/**
 * @category Manager
 * @package App\Manager\User
 * @author Pierre Goupil <p.goupil356@gmail.com>
 *
 * Manager des users. Comporte toutes les méthodes pouvant être nécessaire dans le cadre de la
 * gestion d'un User
 */
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
    private MailerManager $mailerManager;

    public function __construct(
        EntityManagerInterface $em,
        UserPasswordHasherInterface $userPasswordHasher,
        UserRepository $userRepository,
        MailerManager $mailerManager
    )
    {
        parent::__construct($em);
        $this->userPasswordHasher = $userPasswordHasher;
        $this->userRepository = $userRepository;
        $this->mailerManager = $mailerManager;
    }

    /**
     * Fonction de création d'un User
     *
     * @param mixed $data Informations pour l'inscription d'un nouveau User
     *
     * @return User Retourne le nouveau User crée
     */
    public function createUser(mixed $data): User
    {
        // Instancie un nouveau User
        $user = new User();

        // Set ses properties
        $user->setFirstName($data['firstName']);
        $user->setLastName($data['lastName']);
        $user->setEmail($data['email']);

        // Gère le hashage et persitance du password
        $user->setPlainPassword($data['password']);
        $this->hashPassword($user);

        // Test email
        $email = $this->mailerManager->createEmail($user);
        $this->mailerManager->sendEmail($email);


        return $user;
    }

    /**
     * Fonction de Hashage d'un utilisateur
     *
     * @param User $user
     *
     * @return none Pas besoin de retourner quelque chose
     */
    public function hashPassword(User $user)
    {
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $user->getPlainPassword()));
        $user->eraseCredentials();
    }

    /**
     * Fonction qui valide le password passé en payload
     *
     * @param array $data Tableau contenant les infos de la payload
     *
     * @return bool|User
     */
    public function validateUserOldPassword(array $data): bool|User
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

    /**
     * Fonction point d'entrée et execute le changement de password
     *
     * @param User $user
     * @param array $data informations de la payload
     *
     * @return User
     */
    public function changeUserPassword(User $user, array $data):User
    {
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

    /**
     * Update et persist un token JWT en base
     *
     * @param User $user
     * @param string $token
     *
     * @return none
     */
    public function updateJwtToken(User $user, string $token) {
        $user->setJwt($token);
        $this->em->flush();
    }
}
