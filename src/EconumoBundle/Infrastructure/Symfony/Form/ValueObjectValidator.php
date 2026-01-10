<?php

declare(strict_types=1);

namespace App\EconumoBundle\Infrastructure\Symfony\Form;

use DomainException;
use App\EconumoBundle\Domain\Entity\ValueObject\ValueObjectInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ValueObjectValidator
{
    private ?TranslatorInterface $translator = null;

    public function __construct(private readonly string $valueObject)
    {
    }

    public function setTranslator(TranslatorInterface $translator): void
    {
        $this->translator = $translator;
    }

    public function __invoke($value, ExecutionContextInterface $context, $payload): void
    {
        try {
            $this->valueObject::validate($value);
        } catch (DomainException $domainException) {
            if ($this->translator instanceof TranslatorInterface) {
                $message = $this->translator->trans($domainException->getMessage());
            } else {
                $message = $domainException->getMessage();
            }

            $context->buildViolation($message)
                ->atPath($context->getPropertyPath())
                ->addViolation();
        }
    }
}
