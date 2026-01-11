<?php

declare(strict_types=1);

namespace App\EconumoBundle\UI\Service\Validator;

use App\EconumoBundle\UI\Service\Validator\RequestFormValidationHelper;
use App\EconumoBundle\UI\Service\Validator\ValidatorInterface;
use Symfony\Component\Form\FormFactoryInterface;

class Validator implements ValidatorInterface
{
    public function __construct(private readonly FormFactoryInterface $formFactory)
    {
    }

    /**
     * @inheritDoc
     */
    public function validate(string $formClass, array $data, object $model = null): void
    {
        $form = $this->formFactory->create($formClass, $model)->submit($data);
        RequestFormValidationHelper::validate($form);
    }
}
