<?php

declare(strict_types=1);

namespace App\EconumoBundle\UI\Controller\Api\Account\Account\Validation;

use App\EconumoBundle\Domain\Entity\ValueObject\AccountName;
use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;
use App\EconumoBundle\Domain\Entity\ValueObject\Icon;
use App\EconumoBundle\Infrastructure\Symfony\Form\Constraints\OperationId;
use App\EconumoBundle\UI\Service\Validator\ValueObjectValidationFactoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Uuid;

class CreateAccountV1Form extends AbstractType
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
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => AccountName::MAX_LENGTH, 'min' => AccountName::MIN_LENGTH]),
                    $this->valueObjectValidationFactory->create(AccountName::class)],
            ])
            ->add('currencyId', TextType::class, [
                'constraints' => [new NotBlank(), new Uuid()],
            ])
            ->add('balance', TextType::class, [
                'constraints' => [new NotBlank(), $this->valueObjectValidationFactory->create(DecimalNumber::class)]
            ])
            ->add('icon', TextType::class, [
                'constraints' => [new NotBlank(), $this->valueObjectValidationFactory->create(Icon::class)],
            ])
            ->add('folderId', TextType::class, [
                'constraints' => [new NotBlank(), new Uuid()],
            ]);
    }
}
