<?php

declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

//TODO: dodać walidację
class FiltersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('from_date_filter', DateType::class, [
            'widget' => 'single_text',
            'placeholder' => 'RRRR-MM-DD',
        ])
        ->add('to_date_filter', DateType::class, [
            'widget' => 'single_text',
            'placeholder' => 'RRRR-MM-DD',
        ])
        ->add('sort_filter', ChoiceType::class, [
            'choices' => $options['sort_filter'],
            'placeholder' => 'brak',
        // ])
        // ->add('articles_per_page_filter', ChoiceType::class, [
        //     'empty_data' => ['15' => 15],
        //     'choices' => $options['articles_per_page_filter'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'sort_filter' => [
                'Nazwa - rosnąco' => 'a.title-ASC',
                'Nazwa - malejąco' => 'a.title-DESC',
                'Data utworzenia - rosnąco' => 'a.createdAt-ASC',
                'Data utworzenia - malejąco' => 'a.createdAt-DESC',
            ],
            'articles_per_page_filter' => [
                '10' => 10,
                '15' => 15,
                '20' => 20,
                '25' => 25,
                '30' => 30,
            ]
        ]);
    }
}
