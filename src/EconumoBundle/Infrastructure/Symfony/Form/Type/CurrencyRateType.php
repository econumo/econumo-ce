<?php

declare(strict_types=1);

namespace App\EconumoBundle\Infrastructure\Symfony\Form\Type;

use App\EconumoBundle\Application\System\Dto\CurrencyRateRequestDto;
use App\EconumoBundle\Domain\Service\Dto\PositionDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Uuid;

class CurrencyRateType extends AbstractType
{

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'data_class' => CurrencyRateRequestDto::class
        ]);
    }


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('code', TextType::class, [
            'constraints' => [new NotBlank()],
        ])->add('rate', TextType::class, [
            'constraints' => [new NotBlank(), new Type('numeric')],
        ]);
    }
}
