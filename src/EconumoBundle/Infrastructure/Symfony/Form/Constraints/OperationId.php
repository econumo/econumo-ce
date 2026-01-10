<?php

declare(strict_types=1);


namespace App\EconumoBundle\Infrastructure\Symfony\Form\Constraints;

use Symfony\Component\Validator\Constraint;

class OperationId extends Constraint
{
    public string $formatErrorMessage = 'Bad operation id received';

    public string $errorMessage = 'Operation id "{{ string }}" already registered';

    public string $lockedMessage = 'Operation id "{{ string }}" is locked, try request later';
}
