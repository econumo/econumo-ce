<?php

declare(strict_types=1);

namespace App\EconumoBundle\UI\Controller\Api\Budget\ElementCurrency\Validation;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Uuid;

class ChangeElementCurrencyV1Form extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['csrf_protection' => false]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('budgetId', TextType::class, [
                'constraints' => [new NotBlank(), new Uuid()],
            ])
            ->add('elementId', TextType::class, [
                'constraints' => [new NotBlank(), new Uuid()],
            ])
            ->add('currencyId', TextType::class, [
                'constraints' => [new NotBlank(), new Uuid()],
            ]);
    }
}
