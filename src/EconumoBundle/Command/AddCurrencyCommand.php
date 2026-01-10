<?php

declare(strict_types=1);

namespace App\EconumoBundle\Command;

use App\EconumoBundle\Domain\Entity\Currency;
use App\EconumoBundle\Domain\Entity\ValueObject\CurrencyCode;
use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;
use App\EconumoBundle\Domain\Repository\CurrencyRepositoryInterface;
use App\EconumoBundle\Domain\Service\Currency\CurrencyUpdateServiceInterface;
use App\EconumoBundle\Domain\Service\Dto\CurrencyDto;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Intl\Currencies;
use Symfony\Component\Intl\Exception\MissingResourceException;

class AddCurrencyCommand extends Command
{
    protected static $defaultName = 'app:add-currency';

    protected static $defaultDescription = 'Add a new currency';

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
                InputArgument::REQUIRED,
                '3 digit currency code ISO4217 (USD, EUR, check https://en.wikipedia.org/wiki/ISO_4217)'
            )
            ->addArgument(
                'currency-name',
                InputArgument::OPTIONAL,
                'Name of the currency (e.g., Bitcoin, US Dollar). If not provided, the system will use the default currency name if available.'
            )
            ->addArgument(
                'fraction-digits',
                InputArgument::OPTIONAL,
                'Number of decimal places to display for numerical values. If not provided, the system will use the default currency name if available.',
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $code = trim((string)$input->getArgument('currency-code'));
        $name = $input->getArgument('currency-name');
        $fractionDigits = $input->getArgument('fraction-digits');
        try {
            $systemName = Currencies::getName($code);
            $systemSymbol = Currencies::getSymbol($code);
            $systemFractionDigits = Currencies::getFractionDigits($code);
        } catch (MissingResourceException) {
            $systemName = null;
            $systemSymbol = null;
            $systemFractionDigits = null;
        }

        $currencies = [];
        $currencyDto = new CurrencyDto();
        $currencyDto->code = new CurrencyCode($code);
        if (!empty($systemSymbol)) {
            $currencyDto->symbol = $systemSymbol;
        }

        if (!empty($name)) {
            $currencyDto->name = trim((string) $name);
        } elseif ($systemName !== null) {
            $currencyDto->name = $systemName;
        }

        if ($fractionDigits !== null) {
            $currencyDto->fractionDigits = (int) $fractionDigits;
        } elseif ($systemFractionDigits !== null) {
            $currencyDto->fractionDigits = $systemFractionDigits;
        } else {
            $currencyDto->fractionDigits = DecimalNumber::SCALE;
        }

        $currencies[] = $currencyDto;
        $this->currencyUpdateService->updateCurrencies($currencies);
        $currency = $this->currencyRepository->getByCode($currencyDto->code);

        if (!$currency instanceof Currency) {
            $io->error(sprintf("Currency %s wasn't added!", $code));
            return Command::FAILURE;
        }

        $io->success(
            sprintf(
                "Currency %s (%s, %s) successfully created with %d fraction digits! (id: %s)\nExample: %s",
                $code,
                $currency->getName(),
                $currency->getSymbol(),
                $currency->getFractionDigits(),
                $currency->getId()->getValue(),
                number_format((new DecimalNumber(1000.12345678))->drop($currency->getFractionDigits())->float(), $currency->getFractionDigits())
            )
        );
        return Command::SUCCESS;
    }
}
