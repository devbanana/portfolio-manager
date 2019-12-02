<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setAction($options['action'])
            ->add('symbol', TextType::class, [
                'required' => false,
              'attr' => [
                'data-url' => $options['search_url'],
                'data-autocomplete' => 'true',
              ],
            ])
            ->add('submit', SubmitType::class, [
              'label' => 'Search',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'action' => '',
            'search_url' => '',
        ]);
    }
}
