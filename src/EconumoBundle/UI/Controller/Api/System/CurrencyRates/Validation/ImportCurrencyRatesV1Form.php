<?php

declare(strict_types=1);

namespace App\EconumoBundle\UI\Controller\Api\System\CurrencyRates\Validation;

use App\EconumoBundle\Infrastructure\Symfony\Form\Type\CurrencyRateType;
use App\EconumoBundle\UI\Service\Validator\ValueObjectValidationFactoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ImportCurrencyRatesV1Form extends AbstractType
{
    public function __construct(private readonly ValueObjectValidationFactoryInterface $valueObjectValidationFactory)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['csrf_protection' => false]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('timestamp', TextType::class, [
                'constraints' => [new NotBlank()],
            ])
            ->add('base', TextType::class, [
                'constraints' => [new NotBlank()],
            ])
            ->add('items', CollectionType::class, [
                'allow_extra_fields' => true,
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true,
                'entry_type' => CurrencyRateType::class,
            ])
        ;
    }
}
