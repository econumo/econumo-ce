<?php

declare(strict_types=1);

namespace App\EconumoBundle\UI\Controller\Api\Budget\Folder\Validation;

use App\EconumoBundle\Domain\Entity\ValueObject\BudgetFolderName;
use App\EconumoBundle\UI\Service\Validator\ValueObjectValidationFactoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Uuid;

class CreateFolderV1Form extends AbstractType
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
            ->add('budgetId', TextType::class, [
                'constraints' => [new NotBlank(), new Uuid()],
            ])
            ->add('id', TextType::class, [
                'constraints' => [new NotBlank(), new Uuid()],
            ])
            ->add('name', TextType::class, options: [
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => BudgetFolderName::MAX_LENGTH, 'min' => BudgetFolderName::MIN_LENGTH]),
                    $this->valueObjectValidationFactory->create(BudgetFolderName::class)
                ],
            ]);
    }
}
