<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Repository\UserRepository;

class NewArticleForm extends AbstractType
{
  private $userRepository;

  public function __construct(UserRepository $userRepository)
  {
    $this->userRepository = $userRepository;
  }

  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    // $users = array();
    $builder
      ->add('title', TextType::class)
      ->add('author', TextType::class)
      // ->add('author', ChoiceType::class, [
      //       'choices' => [$this->userRepository->findByPartialEmail('noob')],
      //     ])
      ->add('createdAt', DateType::class, [
        'widget' => 'text',
        'format' => 'yyyy-MM-dd'
      ])
      ->add('isPublished', CheckboxType::class, [
        'required' => false
      ])
      ->add('body', TextareaType::class, [
        'required' => false,
        'empty_data' => '[...nie dodano jeszcze treści artykułu...]'
      ])
      // ->add('user', TextType::class)
      ->add('add', SubmitType::class);

      // $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
      //   $article = $event->getData();
      //   $form = $event->getForm();
      //
      //   if ($this->userRepository->findByEmail($article->getAuthor()) !== null) {
      //     $form->add('chooseUser', ChoiceType::class, [
      //       'choices' => [$this->userRepository->findByEmail($article->getAuthor())],
      //     ]);
      //   }
      // });
  }
/**
 * Nie wiem co robi kod poniżej. Przepisałem go z manuala symfony ale jak go
 * wykomentuję to wszystko wydaje się działać tak samo. Btw - czy mogę zadawać
 * ci pytania w takiej formie, czy raczej spisywać na kartce albo wysyłać mailem?
 */
  // public function configureOptions(OptionsResolver $resolver)
  // {
  //   $resolver->setDefaults([
  //     'data_class' => Article::class,
  //   ]);
  // }
}
