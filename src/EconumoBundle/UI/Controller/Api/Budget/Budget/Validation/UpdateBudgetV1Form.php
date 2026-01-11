<?php

declare(strict_types=1);

namespace App\EconumoBundle\UI\Controller\Api\Budget\Budget\Validation;

use App\EconumoBundle\Domain\Entity\ValueObject\BudgetName;
use App\EconumoBundle\UI\Service\Validator\ValueObjectValidationFactoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Uuid;

class UpdateBudgetV1Form extends AbstractType
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
            ->add('id', TextType::class, [
                'constraints' => [new NotBlank(), new Uuid()],
            ])
            ->add('name', TextType::class, options: [
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => BudgetName::MAX_LENGTH, 'min' => BudgetName::MIN_LENGTH]),
                    $this->valueObjectValidationFactory->create(BudgetName::class)
                ],
            ])
            ->add('excludedAccounts', CollectionType::class, [
                'entry_type' => TextType::class,
                'allow_add' => true,
                'constraints' => [
                    new All([
                        'constraints' => [
                            new NotBlank(),
                            new Uuid(),
                        ],
                    ]),
                ],
            ])
            ->add('currencyId', TextType::class, [
                'constraints' => [new NotBlank(), new Uuid()],
            ]);
    }
}
