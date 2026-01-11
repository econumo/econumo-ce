<?php

declare(strict_types=1);

namespace App\EconumoBundle\UI\Controller\Api\Transaction\TransactionList\Validation;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\Uuid;

class GetTransactionListV1Form extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['csrf_protection' => false]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('accountId', TextType::class, [
            'constraints' => [new Uuid()],
        ])->add('periodStart', TextType::class, [
            'constraints' => [new DateTime("Y-m-d H:i:s")],
        ])->add('periodEnd', TextType::class, [
            'constraints' => [new DateTime("Y-m-d H:i:s")],
        ]);
    }
}
