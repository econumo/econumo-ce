<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\User\Assembler;

use Throwable;
use App\EconumoBundle\Application\User\Dto\CurrentUserResultDto;
use App\EconumoBundle\Application\User\Dto\OptionResultDto;
use App\EconumoBundle\Domain\Entity\User;
use App\EconumoBundle\Domain\Entity\UserOption;
use App\EconumoBundle\Domain\Entity\ValueObject\CurrencyCode;
use App\EconumoBundle\Domain\Repository\CurrencyRepositoryInterface;
use App\EconumoBundle\Domain\Service\EncodeServiceInterface;

readonly class CurrentUserToDtoResultAssembler
{
    public function __construct(
        private EncodeServiceInterface $encoder,
        private CurrencyRepositoryInterface $currencyRepository
    ) {
    }


    public function assemble(User $user): CurrentUserResultDto
    {
        $options = [];
        $dto = new CurrentUserResultDto();
        $dto->id = $user->getId()->getValue();
        $dto->name = $user->getName();
        $dto->email = $this->encoder->decode($user->getEmail());
        $dto->avatar = $user->getAvatarUrl();

        $dto->options = [];
        $currencyCode = null;
        foreach ($user->getOptions() as $option) {
            $dto->options[] = $this->getOption($option->getName(), $option->getValue());
            $options[$option->getName()] = $option->getValue();
            if ($option->getName() === UserOption::CURRENCY) {
                $currencyCode = $option->getValue();
            }
        }

        if (!$currencyCode) {
            $currencyCode = UserOption::DEFAULT_CURRENCY;
        }

        try {
            $currency = $this->currencyRepository->getByCode(new CurrencyCode($currencyCode));
        } catch (Throwable) {
            $options[UserOption::CURRENCY] = UserOption::DEFAULT_CURRENCY;
            $currency = $this->currencyRepository->getByCode(new CurrencyCode(UserOption::DEFAULT_CURRENCY));
        }

        $dto->options[] = $this->getOption(UserOption::CURRENCY_ID, $currency->getId()->getValue());

        $dto->currency = $options[UserOption::CURRENCY] ?? UserOption::DEFAULT_CURRENCY;
        $dto->reportPeriod = ($options[UserOption::REPORT_PERIOD] ?? UserOption::DEFAULT_REPORT_PERIOD);

        return $dto;
    }

    private function getOption(string $name, ?string $value): OptionResultDto
    {
        $result = new OptionResultDto();
        $result->name = $name;
        $result->value = $value;
        return $result;
    }
}
