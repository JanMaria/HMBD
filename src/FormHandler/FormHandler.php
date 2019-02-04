<?php

declare(strict_types=1);

namespace App\FormHandler;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Form;
use App\Entity\User;

class FormHandler
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function handleForm(Form $form): void
    {
        $entity = $form->getData();
        $this->manager->persist($entity);
        $this->manager->flush();
    }
}
