<?php

declare(strict_types=1);

namespace App\EconumoBundle\UI\Controller\Api\Transaction\Transaction\Validation;

use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;
use App\EconumoBundle\Domain\Entity\ValueObject\TransactionType;
use App\EconumoBundle\Infrastructure\Symfony\Form\Constraints\OperationId;
use App\EconumoBundle\UI\Service\Validator\ValueObjectValidationFactoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Uuid;

class CreateTransactionV1Form extends AbstractType
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
                'constraints' => [new NotBlank(), new Uuid(), new OperationId()],
            ])
            ->add('type', ChoiceType::class, [
                'constraints' => [new NotBlank()],
                'choices' => [
                    TransactionType::EXPENSE_ALIAS,
                    TransactionType::INCOME_ALIAS,
                    TransactionType::TRANSFER_ALIAS
                ]
            ])
            ->add('amount', TextType::class, [
                'constraints' => [new NotBlank(), $this->valueObjectValidationFactory->create(DecimalNumber::class)]
            ])
            ->add('amountRecipient', TextType::class, [
                'constraints' => [$this->valueObjectValidationFactory->create(DecimalNumber::class)]
            ])
            ->add('accountId', TextType::class, [
                'constraints' => [new NotBlank(), new Uuid()]
            ])
            ->add('accountRecipientId', TextType::class, [
                'constraints' => [new Uuid()]
            ])
            ->add('categoryId', TextType::class, [
                'constraints' => [new Uuid()]
            ])
            ->add('date', TextType::class, [
                'constraints' => [new NotBlank()]
            ])
            ->add('description', TextType::class, [
                'constraints' => [new Type('string'), new Length(['max' => 4096])]
            ])
            ->add('payeeId', TextType::class, [
                'constraints' => [new Uuid()]
            ])
            ->add('tagId', TextType::class, [
                'constraints' => [new Uuid()]
            ])
        ;
    }
}
