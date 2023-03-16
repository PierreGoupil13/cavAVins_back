<?php

namespace App\Controller\User;

use App\Manager\PayloadManager;
use App\Manager\UserManager;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class UserChangePasswordController
{
    public function __invoke(
        Request $request,
        PayloadManager $payloadManager,
        UserManager $userManager,
    )
    {
        $payload = json_decode($request->getContent(), true);
        $data = $payloadManager->extractUserPayload($payload);
        $user = $userManager->validateUserOldPassword($data);
        if(!isset($user)){
            return null;
        }
        $user = $userManager->changeUserPassword($user, $data);
        return $user;
    }
}
