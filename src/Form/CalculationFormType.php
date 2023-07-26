<?php

namespace App\Form;

use App\Enum\CalculatorOperationsEnum;
use App\Form\Model\CalculationFormModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CalculationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('argumentA', NumberType::class, [
                'label' => 'Аргумент 1',
                'required' => false,
                'html5' => true,
            ])
            ->add('operation', ChoiceType::class, [
                'label' => 'Операция',
                'required' => true,
                'choices' => [
                    CalculatorOperationsEnum::ADDITION->value => CalculatorOperationsEnum::ADDITION,
                    CalculatorOperationsEnum::SUBTRACTION->value => CalculatorOperationsEnum::SUBTRACTION,
                    CalculatorOperationsEnum::MULTIPLICATION->value => CalculatorOperationsEnum::MULTIPLICATION,
                    CalculatorOperationsEnum::DIVISION->value => CalculatorOperationsEnum::DIVISION,
                ],
            ])
            ->add('argumentB', NumberType::class, [
                'label' => 'Аргумент 2',
                'required' => false,
                'html5' => true,
            ])
            ->add('add', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary mb-3'],
            ])
            ->add('show', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary mb-3'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CalculationFormModel::class,
        ]);
    }
}
