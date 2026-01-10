<?php

declare(strict_types=1);

namespace App\EconumoBundle\Infrastructure\Symfony\Translation;

use App\EconumoBundle\Domain\Service\Translation\TranslationServiceInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class TranslationService implements TranslationServiceInterface
{
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    /**
     * @inheritDoc
     */
    public function trans(string $id, array $parameters = [], string $domain = null, string $locale = null): string
    {
        return $this->translator->trans($id, $parameters, $domain, $locale);
    }
}
