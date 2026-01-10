<?php

declare(strict_types=1);

namespace App\EconumoBundle\UI\Controller\Api\Payee\PayeeList\Validation;

use App\EconumoBundle\Infrastructure\Symfony\Form\Type\PositionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderPayeeListV1Form extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['csrf_protection' => false]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('changes', CollectionType::class, [
            'allow_extra_fields' => true,
            'allow_add' => true,
            'allow_delete' => true,
            'delete_empty' => true,
            'entry_type' => PositionType::class,
        ]);
    }
}
