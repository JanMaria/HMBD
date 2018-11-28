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
    $id = $form->get('id')->getData();

    $this->manager->flush();

    $this->manager->createQueryBuilder()
      ->update('App\Entity\Article', 'article')
      ->set('article.author', ':myName')
      ->where('article.id = :id')
      ->setParameters([
        'id' => $id,
        'myName' => 'Jan Maria'
        ])
      ->getQuery()
      ->execute();
  }
}
