<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Article;
use App\Entity\User;
use App\Form\DataTransformer\ImageTransformer;
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
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Doctrine\ORM\EntityRepository;

class ArticleType extends AbstractType
{
    private $security;
    private $transformer;

    public function __construct(Security $security, ImageTransformer $transformer)
    {
        $this->security = $security;
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class)
            ->add('createdAt', DateType::class, [
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'invalid_message' => 'Wprowadź datę we wskazanym formacie',
            ])
            ->add('body', TextareaType::class, [
                'required' => false,
                'empty_data' => '[...nie dodano jeszcze treści artykułu...]',
            ])
            ->add('image', FileType::class, [
                'label' => 'Zdjęcie:',
                'required' => false,
                // 'empty_data' => 'uploads/images/default_image.jpeg', #TODO: czy będzie używać tego zdjęcia?
            ])
            ->add('tags', TextareaType::class, [
                'label' => 'Tagi:',
                'required' => false,
                'help' =>
                    'Kolejne tagi należy wypisywać po przecinku i spacji (np.: "jedzenie, wegetarianizm, przepisy")',
            ])
            ->add('save', SubmitType::class);

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options) {
                $article = $event->getData();
                $form = $event->getForm();

                if ($article && $article->getId() !== null) {
                    if ($this->security->isGranted('ROLE_ADMIN')) {
                        $form->add('isPublished', ChoiceType::class, [
                            'choices' => [$options['isPublishedOptions']],
                        ]);
                    } else {
                        $form->add('isPublished', ChoiceType::class, [
                            'choices' => [$options['isPublishedOptions']],
                            'disabled' => true,
                            'mapped' => false,
                        ]);
                    }
                } elseif ($this->security->isGranted('ROLE_ADMIN')) {
                    $form->add('isPublished', CheckboxType::class);
                }

                if ($this->security->isGranted('ROLE_ADMIN')) {
                    $form->add('user', EntityType::class, [
                        'class' => User::class,
                        'choice_label' => function ($user) {
                            $basicLabel = $user->getName().' '.$user->getSurname().' ('.$user->getEmail().')';
                            $fullLabel =
                                (in_array('ROLE_ADMIN', $user->getRoles()) ||
                                in_array('ROLE_SUPER_ADMIN', $user->getRoles())) ?
                                    $basicLabel.' '.implode($user->getRoles(), ' ') : $basicLabel;
                            return $fullLabel;
                        },
                        'data' => $this->security->getUser(),
                    ]);
                } else {
                    $form->add('user', EntityType::class, [
                        'class' => User::class,
                        'query_builder' => function (EntityRepository $er) {
                            return $er
                                ->createQueryBuilder('user')
                                ->where('user = :thisUser')
                                ->setParameter('thisUser', $this->security->getUser());
                        },
                        'choice_label' => 'email',
                        'attr' => ['readonly' => true,],
                    ]);
                }
            });

            $builder
                ->get('tags')
                ->addModelTransformer(new CallbackTransformer(
                    function ($tagsArray) {
                        return ($tagsArray) ? implode(', ', $tagsArray) : null;
                    },
                    function ($tagsString) {
                        return ($tagsString) ? explode(', ', $tagsString) : null;
                    }
                ));

                $builder
                    ->get('image')
                    ->addModelTransformer($this->transformer);
            // $builder
            //     ->get('image')
            //     ->addModelTransformer(new CallbackTransformer(
            //         function ($imageAdress) {
            //             return new File($imageAdress);
            //         },
            //         function ($image) {
            //             $imageName = md5(uniqid()).'.'.$image->guessExtension();
            //             // $imageAdress = 'uploads/images/'.$imageName;
            //             $image->move('uploads/images/', $imageName);
            //         }
            //     ));
    }





    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
            'isPublishedOptions' => [
                'Tak' => true,
                'Nie' => false,
            ],
        ]);
    }
}
