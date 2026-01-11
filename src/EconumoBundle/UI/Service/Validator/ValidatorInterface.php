<?php

declare(strict_types=1);

namespace App\EconumoBundle\UI\Service\Validator;

use App\EconumoBundle\Application\Exception\ValidationException;

interface ValidatorInterface
{
    /**
     * @param string $formClass
     * @param array $data
     * @param object|null $model
     * @throws ValidationException
     */
    public function validate(string $formClass, array $data, object $model = null): void;
}
