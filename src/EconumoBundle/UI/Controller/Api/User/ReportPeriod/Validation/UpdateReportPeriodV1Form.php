<?php

declare(strict_types=1);

namespace App\EconumoBundle\UI\Controller\Api\User\ReportPeriod\Validation;

use App\EconumoBundle\Domain\Entity\ValueObject\ReportPeriod;
use App\EconumoBundle\UI\Service\Validator\ValueObjectValidationFactoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Uuid;

class UpdateReportPeriodV1Form extends AbstractType
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
        $builder->add('value', TextType::class, [
            'constraints' => [new NotBlank(), $this->valueObjectValidationFactory->create(ReportPeriod::class)],
        ]);
    }
}
