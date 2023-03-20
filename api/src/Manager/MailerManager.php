<?php

namespace App\Manager;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
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

    // A modifier, c'est la version ultra basique
    public function createEmail(User $user)
    {
        $email = (new Email())
            ->subject('Test')
            ->from('p.goupil356@gmail.com')
            ->to($user->getEmail())
            ->text($user->getFirstName())
            ->html('<p>See Twig integration for better HTML integration!</p>');

        return $email;
    }

    public function sendEmail(Email $email)
    {
        $this->mailer->send($email);
    }


}
