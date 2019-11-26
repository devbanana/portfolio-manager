<?php

namespace App\Form;

use App\Entity\Account;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class AccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('type', null, [
                'placeholder' => '--- Choose One ---',
            ])
            ->add('allocationType', ChoiceType::class, [
                'choices' => [
                    'Cost-based' => Account::ALLOCATION_COST,
                    'Value-based' => Account::ALLOCATION_VALUE,
                ],
                'expanded' => true,
                'help' => 'Should assets of this account be allocated by cost-basis or total value?',
            ])
            ->add('allocationPercent', PercentType::class, [
                'scale' => 2,
                'help' => 'What percent of your total portfolio should be comprised by this account?',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Account::class,
        ]);
    }
}
