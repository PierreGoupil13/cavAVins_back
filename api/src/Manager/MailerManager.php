<?php

namespace App\Manager;

use App\Entity\ResetToken;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerManager extends Manager
{
    private MailerInterface $mailer;

    public function __construct(
        MailerInterface $mailer,
        EntityManagerInterface $em
    )
    {
        parent::__construct($em);
        $this->mailer = $mailer;
    }

    public function sendEmail(Email $email)
    {
        $this->mailer->send($email);
    }
    // A modifier, c'est la version ultra basique
    public function createSignUpEmail(User $user)
    {
        $email = (new TemplatedEmail())
            ->subject('Signing up to CaveAVins')
            ->from('contactcave@gmail.com')
            ->to($user->getEmail())
            ->htmlTemplate('emails/signup.html.twig')
            ->context([
                'user' => $user,
                "reset" => "https://youtube.com"
            ]);

        return $email;
    }

    public function sendResetEmail(User $user, ResetToken $token)
    {
        $email = (new TemplatedEmail())
            ->subject('Signing up to CaveAVins')
            ->from('contactcave@gmail.com')
            ->to($user->getEmail())
            ->htmlTemplate('emails/reset.html.twig')
            ->context([
                'user' => $user,
                "reset" => "cavedomain/reset_password?". $token->getJwtToken()
            ]);

        $this->sendEmail($email);
    }

}
