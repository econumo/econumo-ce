<?php

declare(strict_types=1);

namespace App\EconumoBundle\UI\Controller\Api\System\User\Validation;

use App\EconumoBundle\UI\Service\Validator\ValueObjectValidationFactoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CreateUserV1Form extends AbstractType
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
        $builder->add('email', TextType::class, [
            'constraints' => [new NotBlank(), new Email(), new Length(['max' => 256])],
        ])->add('password', TextType::class, [
            'constraints' => [new NotBlank(), new Length(['min' => 4])],
        ])->add('name', TextType::class, [
            'constraints' => [new NotBlank(), new Length(['min' => 3, 'max' => '20'])],
        ]);
    }
}
