<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\User\Assembler;

use App\EconumoBundle\Application\User\Dto\GetOptionListV1RequestDto;
use App\EconumoBundle\Application\User\Dto\GetOptionListV1ResultDto;
use App\EconumoBundle\Application\User\Assembler\OptionToDtoResultAssembler;
use App\EconumoBundle\Domain\Entity\UserOption;

class GetOptionListV1ResultAssembler
{
    public function __construct(private readonly OptionToDtoResultAssembler $optionToDtoResultAssembler)
    {
    }

    /**
     * @param UserOption[] $options
     */
    public function assemble(
        GetOptionListV1RequestDto $dto,
        array $options
    ): GetOptionListV1ResultDto {
        $result = new GetOptionListV1ResultDto();
        $result->items = [];
        foreach ($options as $option) {
            $result->items[] = $this->optionToDtoResultAssembler->assemble($option);
        }

        return $result;
    }
}
