<?php
declare(strict_types=1);

namespace App\EconumoBundle\Application\Exception;

use App\EconumoBundle\Application\Exception\ApplicationException;
use Throwable;

/**
 * Class ValidationException
 * @package App\EconumoBundle\Application
 */
class ValidationException extends ApplicationException
{
    /**
     * ValidationException constructor.
     */
    public function __construct(string $message = '', int $code = 400, ?Throwable $previous = null, protected array $errors = [])
    {
        parent::__construct($message, $code, $previous);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getErrorsAsString(): string
    {
        $result = '';
        foreach ($this->errors as $key => $values) {
            $result .= sprintf('[%s: %s]', $key, implode(' ', $values));
        }

        return $result;
    }
}
