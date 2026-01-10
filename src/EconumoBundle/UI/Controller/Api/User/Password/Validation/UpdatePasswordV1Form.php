<?php

declare(strict_types=1);

namespace App\EconumoBundle\UI\Controller\Api\User\Password\Validation;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UpdatePasswordV1Form extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['csrf_protection' => false]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('oldPassword', TextType::class, [
            'constraints' => [new NotBlank()],
        ])->add('newPassword', TextType::class, [
            'constraints' => [new NotBlank(), new Length(['min' => 4])],
        ]);
    }
}
