<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\System\Assembler;

use App\EconumoBundle\Application\System\Dto\CreateUserV1ResultDto;
use App\EconumoBundle\Domain\Entity\User;

readonly class CreateUserV1ResultAssembler
{
    public function assemble(User $user): CreateUserV1ResultDto
    {
        $result = new CreateUserV1ResultDto();
        $result->id = $user->getId()->getValue();

        return $result;
    }
}
