<?php

declare(strict_types=1);

namespace App\EconumoToolsBundle\Command;

use App\EconumoToolsBundle\Domain\Service\CurrencyRatesLoaderServiceInterface;
use App\EconumoBundle\Domain\Service\Currency\CurrencyRatesUpdateServiceInterface;
use DateTimeImmutable;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UpdateCurrencyRatesCommand extends Command
{
    protected static $defaultName = 'app:update-currency-rates';

    protected static $defaultDescription = 'Load and update currency rates';

    public function __construct(
        private readonly CurrencyRatesLoaderServiceInterface $currencyRatesLoaderService,
        private readonly CurrencyRatesUpdateServiceInterface $currencyRatesUpdateService
    ) {
        parent::__construct(self::$defaultName);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('date', InputArgument::OPTIONAL, 'Date (Y-m-d)', date('Y-m-d'));
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $date = DateTimeImmutable::createFromFormat('Y-m-d', $input->getArgument('date'));
        $currencyRates = $this->currencyRatesLoaderService->loadCurrencyRates($date);
        $io->info(sprintf('Loaded %d currency rates', count($currencyRates)));
        $updatedCnt = $this->currencyRatesUpdateService->updateCurrencyRates($currencyRates);
        $io->success(sprintf('Updated %d currency rates', $updatedCnt));

        return Command::SUCCESS;
    }
}