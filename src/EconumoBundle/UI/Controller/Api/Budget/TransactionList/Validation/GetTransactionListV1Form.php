<?php

declare(strict_types=1);

namespace App\EconumoBundle\UI\Controller\Api\Budget\TransactionList\Validation;

use App\EconumoBundle\UI\Service\Validator\ValueObjectValidationFactoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Uuid;

class GetTransactionListV1Form extends AbstractType
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
            ->add('periodStart', TextType::class, [
                'constraints' => [new NotBlank(), new DateTime("Y-m-d")]
            ])
            ->add('categoryId', TextType::class, [
                'constraints' => [new Uuid()],
            ])
            ->add('tagId', TextType::class, [
                'constraints' => [new Uuid()],
            ])
            ->add('envelopeId', TextType::class, [
                'constraints' => [new Uuid()],
            ]);
    }
}
