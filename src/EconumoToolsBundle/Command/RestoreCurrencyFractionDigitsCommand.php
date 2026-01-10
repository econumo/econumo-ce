<?php

declare(strict_types=1);

namespace App\EconumoToolsBundle\Command;

use App\EconumoBundle\Domain\Entity\Currency;
use App\EconumoBundle\Domain\Entity\ValueObject\CurrencyCode;
use App\EconumoBundle\Domain\Repository\CurrencyRepositoryInterface;
use App\EconumoBundle\Domain\Service\Currency\CurrencyUpdateServiceInterface;
use App\EconumoBundle\Domain\Service\Dto\CurrencyDto;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RestoreCurrencyFractionDigitsCommand extends Command
{
    protected static $defaultName = 'app:restore-currency-fraction-digits';

    protected static $defaultDescription = "Restore the system's currency fraction digits";

    public function __construct(
        private readonly CurrencyUpdateServiceInterface $currencyUpdateService,
        private readonly CurrencyRepositoryInterface $currencyRepository
    ) {
        parent::__construct(self::$defaultName);
    }


    protected function configure(): void
    {
        $this
            ->addArgument(
                'currency-code',
                InputArgument::IS_ARRAY,
                '3 digit currency code ISO4217 (USD, EUR, check https://en.wikipedia.org/wiki/ISO_4217)'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $codes = (array) $input->getArgument('currency-code');

        $currencies = [];
        if ($codes !== []) {
            foreach ($codes as $code) {
                $currencyDto = new CurrencyDto();
                $currencyDto->code = new CurrencyCode($code);
                $currencyDto->symbol = '';
                $currencies[] = $currencyDto;
            }
        } else {
            foreach ($this->currencyRepository->getAll() as $currency) {
                $currencyDto = new CurrencyDto();
                $currencyDto->code = $currency->getCode();
                $currencyDto->symbol = $currency->getSymbol();
                $currencies[] = $currencyDto;
            }
        }

        $this->currencyUpdateService->restoreFractionDigits($currencies);

        $success = true;
        foreach ($currencies as $dto) {
            $currency = $this->currencyRepository->getByCode($dto->code);
            if (!$currency instanceof Currency) {
                $io->error(sprintf("Currency %s wasn't updated!", $dto->code->getValue()));
                $success = false;
            } else {
                $io->success(
                    sprintf(
                        'Currency %s (%s, %s) successfully updated with %d fraction! (id: %s)',
                        $currency->getCode()->getValue(),
                        $currency->getName(),
                        $currency->getSymbol(),
                        $currency->getFractionDigits(),
                        $currency->getId()->getValue()
                    )
                );
            }
        }

        if (!$success) {
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
