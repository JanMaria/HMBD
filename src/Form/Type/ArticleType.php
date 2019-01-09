<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Article;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Date;
// use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

class ArticleType extends AbstractType
{
    // private $security;
    //
    // public function __construct(Security $security)
    // {
    //     $this->security = $security;
    // }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class)
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
            ])
            ->add('createdAt', DateType::class, [
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'invalid_message' => 'Wprowadź datę we wskazanym formacie',
            ])
            ->add('body', TextareaType::class, [
                'required' => false,
                'empty_data' => '[...nie dodano jeszcze treści artykułu...]',
            ])
            ->add('save', SubmitType::class);

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options) {
                $article = $event->getData();
                $form = $event->getForm();

                if (!$article) {
                    $form->add('isPublished', CheckboxType::class, [
                        'required' => false
                    ]);
                } else {
                    $form->add('isPublished', ChoiceType::class, ['choices' => $options['isPublishedOptions']]);
                }
            });
    }





    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
            'isPublishedOptions' => null,
        ]);
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
