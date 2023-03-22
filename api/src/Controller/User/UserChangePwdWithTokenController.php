<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Manager\MailerManager;
use App\Manager\PayloadManager;
use App\Manager\ResetTokenManager;
use App\Manager\UserManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class UserChangePwdWithTokenController
{
    public function __invoke(
        ResetTokenManager $resetTokenManager,
        UserManager $userManager,
        PayloadManager $payloadManager,
        MailerManager $mailer,
        Request $request
    )
    {
        // Gerer la payload et recupère le User
        $payload = json_decode($request->getContent(), true);
        $data = $payloadManager->extractUserPayload($payload);
        // Si erreur dans le user ça pète une erreur
        $user = $userManager->getUserByEmail($data);

        // Check si un token existe pour l'email
        // Si non le créer et persister
        if (!$resetTokenManager->resetTokenExist($user)) {
            $token = $resetTokenManager->createResetToken($user);

            $mailer->sendResetEmail($user,$token);
            // Le return le fait persister en base
            return $token;
        }
        // Sinon verifier et changer password
        if($resetTokenManager->resetTokenExist($user)){
            $userManager->changeUserPassword($user,$data);
            //supprimer le token jwt de reset
            $token = $resetTokenManager->getResetToken($user);
            $resetTokenManager->removeResetToken($token);
        }
        return($user);
    }
}
