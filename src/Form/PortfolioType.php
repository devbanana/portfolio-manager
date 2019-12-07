<?php

namespace App\Form;

use App\Entity\Portfolio;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PortfolioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('cashReserve', null, [
                'help' => 'Is this portfolio for your cash reserve? There must be at least one.',
            ])
            ->add('unallocated', null, [
                'label' => 'Auto-allocate',
                'help' => 'If checked, then the allocation will be automatically calculated.',
            ])
            ->add('allocationPercent', PercentType::class, [
                'scale' => 2,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Portfolio::class,
        ]);
    }
}
