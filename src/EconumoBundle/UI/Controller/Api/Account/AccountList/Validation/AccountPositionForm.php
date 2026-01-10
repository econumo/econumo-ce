<?php

declare(strict_types=1);


namespace App\EconumoBundle\UI\Controller\Api\Account\AccountList\Validation;

use App\EconumoBundle\Domain\Service\Dto\AccountPositionDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Uuid;

class AccountPositionForm extends AbstractType
{

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'data_class' => AccountPositionDto::class
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('id', TextType::class, [
            'constraints' => [new NotBlank(), new Uuid()],
        ])->add('folderId', TextType::class, [
            'constraints' => [new NotBlank(), new Uuid()],
        ])->add('position', TextType::class, [
            'constraints' => [new NotBlank(), new Type('numeric')],
        ]);
    }
}
