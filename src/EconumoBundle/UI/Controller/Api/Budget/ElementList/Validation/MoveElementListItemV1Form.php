<?php

declare(strict_types=1);

namespace App\EconumoBundle\UI\Controller\Api\Budget\ElementList\Validation;

use App\EconumoBundle\Application\Budget\Dto\MoveElementListItemV1RequestDto;
use App\EconumoBundle\Domain\Entity\ValueObject\BudgetElementType;
use App\EconumoBundle\UI\Service\Validator\ValueObjectValidationFactoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Uuid;

class MoveElementListItemV1Form extends AbstractType
{
    public function __construct(private readonly ValueObjectValidationFactoryInterface $valueObjectValidationFactory)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MoveElementListItemV1RequestDto::class,
            'csrf_protection' => false
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Uuid(),
                ],
            ])
            ->add('position', IntegerType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Type('integer'),
                    new PositiveOrZero(),
                ],
            ])
            ->add('folderId', TextType::class, [
                'constraints' => [
                    new Uuid(),
                ],
                'required' => false,
            ]);
    }
}
