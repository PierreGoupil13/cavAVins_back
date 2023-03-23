<?php

namespace App\Manager;

use Doctrine\ORM\EntityManagerInterface;


abstract class Manager
{
    protected EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
}
