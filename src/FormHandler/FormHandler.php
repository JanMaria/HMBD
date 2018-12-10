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
    // $form->getData()->setAuthor('Jan Maria');
    $user = $this->manager->getRepository(User::class)->findOneBy(['email' => $form->getData()->getAuthorEmail()]);
    $form->getData()->setUser($user);

    $this->manager->flush();
  }

}
