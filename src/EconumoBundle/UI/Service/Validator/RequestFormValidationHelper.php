<?php

declare(strict_types=1);

namespace App\EconumoBundle\UI\Service\Validator;

use App\EconumoBundle\Application\Exception\ValidationException;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Response;

class RequestFormValidationHelper
{
    public static function getFlatArrayErrors(FormView $formView): array
    {
        $values = [];
        /** @var FormView $subView */
        foreach ($formView->vars['form']->children as $subView) {
            if ($subView->children !== []) {
                $values = array_merge($values, static::getFlatArrayErrors($subView));
            } elseif (!empty($subView->vars['errors'])) {
                foreach ($subView->vars['errors'] as $error) {
                    /** @var FormError $error */
                    $values[$subView->vars['name']][] = $error->getMessage();
                }
            }
        }

        return $values;
    }

    /**
     * @throws ValidationException
     */
    public static function validate(FormInterface $form): void
    {
        if (!$form->isValid()) {
            $errors = static::getFlatArrayErrors($form->createView());
            throw new ValidationException(
                'Form validation error',
                Response::HTTP_BAD_REQUEST,
                null,
                $errors
            );
        }
    }
}
