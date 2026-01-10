<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Connection;

use App\EconumoBundle\Application\Connection\Dto\DeleteConnectionV1RequestDto;
use App\EconumoBundle\Application\Connection\Dto\DeleteConnectionV1ResultDto;
use App\EconumoBundle\Application\Connection\Assembler\DeleteConnectionV1ResultAssembler;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Service\Connection\ConnectionServiceInterface;

readonly class ConnectionService
{
    public function __construct(
        private DeleteConnectionV1ResultAssembler $deleteConnectionV1ResultAssembler,
        private ConnectionServiceInterface $connectionService
    ) {
    }

    public function deleteConnection(
        DeleteConnectionV1RequestDto $dto,
        Id $userId
    ): DeleteConnectionV1ResultDto {
        $this->connectionService->delete($userId, new Id($dto->id));
        return $this->deleteConnectionV1ResultAssembler->assemble($dto);
    }
}
