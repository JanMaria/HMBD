<?php

declare(strict_types=1);

namespace App\FormHandler;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Form;

class FormHandler
{
  private $manager;

  public function __construct(EntityManagerInterface $manager)
  {
    $this->manager = $manager;
  }

  public function handleForm(Form $form): void
  {
    $form->getData()->setAuthor('Jan Maria');

    $this->manager->flush();
  }
  
}
